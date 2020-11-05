<div 
  class="wcpt-editor-row-option wcpt-search-rules__custom-rules"
  wcpt-model-key="rules"
>

  <span class="wcpt-search-rules__match">Match</span>
  <span class="wcpt-search-rules__score">Score</span>

  <!-- phrase exact -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="phrase_exact_enabled"> 
      <span class="wcpt-search-rules__match-name">Phrase exact</span>
      <span class="wcpt-search-rules__match-description">$term === "$keyword_phrase"</span>
      <input type="number" min="0" wcpt-model-key="phrase_exact_score">
    </label>
  </div>

  <!-- phrase like -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="phrase_like_enabled"> 
      <span class="wcpt-search-rules__match-name">Phrase like</span>
      <span class="wcpt-search-rules__match-description">$term = $word "...{$keyword_phrase}..." $word</span>
      <input type="number" min="0" wcpt-model-key="phrase_like_score">
    </label>
  </div>

  <!-- keyword exact -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="keyword_exact_enabled"> 
      <span class="wcpt-search-rules__match-name">Keyword exact</span>
      <span class="wcpt-search-rules__match-description">$term = $word "$keyword" $word</span>
      <input type="number" min="0" wcpt-model-key="keyword_exact_score">
    </label>
  </div>

  <!-- keyword like -->
  <div class="wcpt-editor-row-option">
    <label>
      <input type="checkbox" wcpt-model-key="keyword_like_enabled"> 
      <span class="wcpt-search-rules__match-name">Keyword like</span>
      <span class="wcpt-search-rules__match-description">$term = $word "...{$keyword}..." $word</span>
      <input type="number" min="0" wcpt-model-key="keyword_like_score">
    </label>
  </div>

</div>