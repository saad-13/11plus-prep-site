<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <h2>Learn</h2>
  <div class="accordion" id="subjectsAccordion">
    <!-- Maths -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingMaths">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaths" aria-expanded="false" aria-controls="collapseMaths">
          Maths
        </button>
      </h2>
      <div id="collapseMaths" class="accordion-collapse collapse" aria-labelledby="headingMaths" data-bs-parent="#subjectsAccordion">
        <div class="accordion-body">
          <ul>
            <li><a href="topic.php?subject=maths&topic=algebra">Algebra</a></li>
            <li><a href="topic.php?subject=maths&topic=geometry">Geometry</a></li>
            <!-- Add more topics -->
          </ul>
        </div>
      </div>
    </div>
    <!-- English -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingEnglish">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnglish" aria-expanded="false" aria-controls="collapseEnglish">
          English
        </button>
      </h2>
      <div id="collapseEnglish" class="accordion-collapse collapse" aria-labelledby="headingEnglish" data-bs-parent="#subjectsAccordion">
        <div class="accordion-body">
          <ul>
            <li><a href="topic.php?subject=english&topic=grammar">Grammar</a></li>
            <li><a href="topic.php?subject=english&topic=vocabulary">Vocabulary</a></li>
            <!-- Add more topics -->
          </ul>
        </div>
      </div>
    </div>
    <!-- Repeat similar blocks for Verbal and Non-Verbal Reasoning -->
  </div>
</div>
<?php include 'includes/footer.php'; ?>
