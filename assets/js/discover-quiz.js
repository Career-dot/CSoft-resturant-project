/**
 * Taste Personality quiz: one-question-at-a-time stepper, progress
 * bar, then a POST to /api/taste.php for the scored result.
 */
(function () {
  const form = document.getElementById('tasteQuizForm');
  const steps = Array.from(document.querySelectorAll('.quiz-step'));
  const progress = document.getElementById('quizProgress');
  const stepCount = document.getElementById('quizStepCount');
  const backBtn = document.getElementById('quizBack');
  const nextBtn = document.getElementById('quizNext');
  const resultWrap = document.getElementById('tasteResult');

  if (!form || !steps.length) return;

  let current = 0;

  function updateUI() {
    steps.forEach((s, i) => (s.hidden = i !== current));
    progress.style.width = `${((current + 1) / steps.length) * 100}%`;
    stepCount.textContent = `Question ${current + 1} of ${steps.length}`;
    backBtn.disabled = current === 0;
    nextBtn.textContent = current === steps.length - 1 ? 'See My Result' : 'Next';
  }

  function currentAnswered() {
    return !!steps[current].querySelector('input:checked');
  }

  backBtn.addEventListener('click', () => {
    if (current > 0) {
      current -= 1;
      updateUI();
    }
  });

  nextBtn.addEventListener('click', () => {
    if (!currentAnswered()) {
      steps[current].classList.add('quiz-step--shake');
      setTimeout(() => steps[current].classList.remove('quiz-step--shake'), 300);
      return;
    }
    if (current < steps.length - 1) {
      current += 1;
      updateUI();
    } else {
      submitQuiz();
    }
  });

  function collectAnswers() {
    const answers = {};
    new FormData(form).forEach((value, key) => {
      answers[key] = value;
    });
    return answers;
  }

  async function submitQuiz() {
    nextBtn.disabled = true;
    nextBtn.textContent = 'Reading your taste…';

    try {
      const res = await fetch(`${window.APP_BASE}/api/taste.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ answers: collectAnswers() }),
      });
      if (!res.ok) throw new Error(`Request failed: ${res.status}`);
      const result = await res.json();
      renderResult(result);
    } catch (err) {
      nextBtn.disabled = false;
      nextBtn.textContent = 'See My Result';
      console.error('[zaiqa] taste quiz submit failed:', err);
      alert('Couldn\u2019t read your taste profile right now — please try again.');
    }
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  function renderResult(r) {
    form.hidden = true;
    document.querySelector('.taste-quiz__progress-wrap').hidden = true;
    document.getElementById('quizStepCount').hidden = true;

    resultWrap.hidden = false;
    resultWrap.innerHTML = `
      <p class="taste-result__eyebrow">Your Taste Personality</p>
      <h2 class="taste-result__name">${escapeHtml(r.name)}</h2>
      <p class="taste-result__desc">${escapeHtml(r.description)}</p>

      <div class="taste-result__cards">
        <div class="taste-result__card">
          <p class="taste-result__card-label">Recommended Dishes</p>
          <p class="taste-result__card-value">${r.dishes.map(escapeHtml).join(', ')}</p>
        </div>
        <div class="taste-result__card">
          <p class="taste-result__card-label">Recommended Drink</p>
          <p class="taste-result__card-value">${escapeHtml(r.drink)}</p>
        </div>
        <div class="taste-result__card">
          <p class="taste-result__card-label">Spice Preference</p>
          <p class="taste-result__card-value">${escapeHtml(r.spice_pref)}</p>
        </div>
        <div class="taste-result__card">
          <p class="taste-result__card-label">Your Dining Experience</p>
          <p class="taste-result__card-value">${escapeHtml(r.dining_experience)}</p>
        </div>
      </div>

      <div class="taste-result__actions">
        <a href="${window.APP_BASE}/pages/menu.php" class="btn btn--primary">Explore the Menu</a>
        <button type="button" class="btn btn--ghost" id="retakeQuiz">Retake the Quiz</button>
      </div>

      ${window.APP_USER
        ? '<p class="panel-empty" style="text-align:center;margin-top:1rem">Saved to your My Taste profile.</p>'
        : `<p class="panel-empty" style="text-align:center;margin-top:1rem"><a href="${window.APP_BASE}/pages/login.php">Log in</a> to save this result to your profile.</p>`
      }
    `;

    document.getElementById('retakeQuiz').addEventListener('click', () => {
      form.reset();
      current = 0;
      form.hidden = false;
      document.querySelector('.taste-quiz__progress-wrap').hidden = false;
      document.getElementById('quizStepCount').hidden = false;
      resultWrap.hidden = true;
      nextBtn.disabled = false;
      updateUI();
    });
  }

  updateUI();
})();
