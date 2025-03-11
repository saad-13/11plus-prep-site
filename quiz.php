<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

// Determine quiz type from URL parameters:
$quizType = '';
$title = '';
$description = '';
$numQuestions = 0;
$questions = [];

if (isset($_GET['type'])) {
    // For AI practice tests or Mock Exams
    $quizType = $_GET['type']; // Expected values: 'ai' or 'mock'
    if ($quizType === 'ai') {
        $title = "AI Practice Tests";
        $description = "Engage with AI-driven practice tests.";
    } elseif ($quizType === 'mock') {
        $title = "Mock Exams";
        $description = "Simulate the exam experience with full-length tests.";
    }
    $numQuestions = 100;
    // Query to fetch 100 random questions from all 4 subjects.
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE subject IN ('mathematics','english','verbal','nonverbal') ORDER BY RAND() LIMIT ?");
    $stmt->execute([$numQuestions]);
    $questions = $stmt->fetchAll();
} elseif (isset($_GET['subject'])) {
    // For subject-specific quizzes (e.g., mathematics, english, etc.)
    $subject = $_GET['subject'];
    $quizType = 'subject';
    $title = ucfirst($subject) . " Practice";
    $description = "Test your skills in " . ucfirst($subject) . ".";
    $numQuestions = 10;
    
    // Retrieve the current user's difficulty level for this subject from user_difficulty.
    $stmtDiff = $pdo->prepare("SELECT difficulty_level FROM user_difficulty WHERE user_id = ? AND quiz_type = ?");
    $stmtDiff->execute([$_SESSION['user_id'], $subject]);
    $userDiff = $stmtDiff->fetchColumn();
    
    // If no record exists, default to 1 and insert it.
    if (!$userDiff) {
        $userDiff = 1;
        $stmtInsert = $pdo->prepare("INSERT INTO user_difficulty (user_id, quiz_type, difficulty_level) VALUES (?, ?, ?)");
        $stmtInsert->execute([$_SESSION['user_id'], $subject, $userDiff]);
    }
    
    // Use the new SQL query that filters by difficulty level and subject.
    $stmt = $pdo->prepare("SELECT * FROM questions 
                           WHERE difficulty_level = ? 
                             AND subject = ?
                           ORDER BY RAND() LIMIT ?");
    $stmt->execute([$userDiff, $subject, $numQuestions]);
    $questions = $stmt->fetchAll();
} else {
    // If no valid parameter, redirect back to the Practice page.
    header("Location: practice.php");
    exit;
}
?>

<div class="wrapper">
  <div class="content">
    <div class="container mt-4">
      <div class="d-flex align-items-center mb-3">
        <!-- Back button: returns to practice.php -->
        <a href="practice.php" class="btn btn-secondary me-3">Back</a>
        <h2 class="mb-0"><?php echo htmlspecialchars($title); ?></h2>
      </div>
      <p class="fst-italic"><?php echo htmlspecialchars($description); ?></p>
      
      <!-- Quiz Interface -->
      <div id="quiz-container">
        <!-- Area where the current question will be displayed -->
        <div id="question-display"></div>
        
        <!-- Navigation Buttons -->
        <div class="mt-3">
          <span id="question-number"></span>
          <button id="skip-btn" class="btn btn-warning">Skip</button>
          <button id="next-btn" class="btn btn-primary">Next</button>
          <button id="complete-btn" class="btn btn-success" style="display:none;">Complete Quiz</button>
        </div>
      </div>
    </div>

    <script>
    // Pass the PHP questions array into JavaScript.
    const questions = <?php echo json_encode($questions); ?>;
    let currentQuestionIndex = 0;
    let userAnswers = [];

    // Function to display a question.
    function displayQuestion(index) {
      const container = document.getElementById('question-display');
      const question = questions[index];
      
      // Parse options from JSON if they exist (otherwise fallback to a default array)
      let options;
      try {
        options = question.options ? JSON.parse(question.options) : ["Option A", "Option B", "Option C", "Option D"];
      } catch (e) {
        console.error("Error parsing options for question:", question, e);
        options = ["Option A", "Option B", "Option C", "Option D"];
      }
      
      // Construct the HTML for the question.
      let html = `<div class="card">
          <div class="card-body">
            <h5 class="card-title">Question ${index + 1}: ${question.question_text}</h5>
            <form id="answer-form">`;
      
      options.forEach((option, i) => {
        html += `<div class="form-check">
          <input class="form-check-input" type="radio" name="option" id="option${i}" value="${option}">
          <label class="form-check-label" for="option${i}">${option}</label>
        </div>`;
      });
      
      html += `</form>
          </div>
        </div>`;
      
      container.innerHTML = html;
      document.getElementById('question-number').innerText = `Question ${index + 1} of ${questions.length}`;
      
      // Show/Hide Next and Complete buttons.
      document.getElementById('next-btn').style.display = (index < questions.length - 1) ? 'inline-block' : 'none';
      document.getElementById('complete-btn').style.display = (index === questions.length - 1) ? 'inline-block' : 'none';
    }

    // Event listener for the "Next" button.
    document.getElementById('next-btn').addEventListener('click', () => {
      const form = document.getElementById('answer-form');
      const formData = new FormData(form);
      const answer = formData.get('option') || null;
      userAnswers[currentQuestionIndex] = answer;
      currentQuestionIndex++;
      displayQuestion(currentQuestionIndex);
    });

    // Event listener for the "Skip" button.
    document.getElementById('skip-btn').addEventListener('click', () => {
      userAnswers[currentQuestionIndex] = null; // Mark as skipped.
      currentQuestionIndex++;
      if (currentQuestionIndex < questions.length) {
        displayQuestion(currentQuestionIndex);
      } else {
        document.getElementById('complete-btn').style.display = 'inline-block';
        document.getElementById('next-btn').style.display = 'none';
      }
    });

    document.getElementById('complete-btn').addEventListener('click', () => {
      // Ensure we capture an answer from the current question (if one exists)
      const form = document.getElementById('answer-form');
      if (form) {
        const formData = new FormData(form);
        const answer = formData.get('option') || null;
        userAnswers[currentQuestionIndex] = answer;
      }
      
      // Calculate the results by comparing userAnswers with each question's correct_answer.
      let correctCount = 0;
      let details = [];
      
      questions.forEach((question, index) => {
        let selectedAnswer = userAnswers[index];
        let isCorrect = selectedAnswer === question.correct_answer;
        if (isCorrect) {
          correctCount++;
        }
        details.push({
          question_id: question.id,
          question_text: question.question_text,
          selected_answer: selectedAnswer,
          correct_answer: question.correct_answer,
          is_correct: isCorrect,
          explanation: question.explanation
        });
      });
      
      // Prepare a payload to send to the server.
      let payload = {
        quiz_type: '<?php echo isset($quizType) ? $quizType : ''; ?>', // "ai", "mock", or "subject"
        subject: '<?php echo isset($subject) ? $subject : ''; ?>', // Only for subject-specific quizzes
        score: correctCount,
        total_questions: questions.length,
        details: details
      };
      
      // Send the results to process_quiz.php using fetch.
      fetch('process_quiz.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Redirect to the quiz results page, passing the result id in the query string.
          window.location.href = "quiz_results.php?result_id=" + data.result_id;
        } else {
          alert('Error processing quiz results. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error processing quiz results. Please try again.');
      });
    });


    // Start the quiz by displaying the first question.
    if (questions.length > 0) {
      displayQuestion(currentQuestionIndex);
    } else {
      document.getElementById('quiz-container').innerHTML = "<p>No questions found.</p>";
    }
    </script>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>