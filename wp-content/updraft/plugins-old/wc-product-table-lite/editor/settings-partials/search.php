<div class="wcpt-toggle-options wcpt-search-rules" wcpt-model-key="search">

  <div class="wcpt-editor-light-heading wcpt-toggle-label">
    Search 
    <?php wcpt_pro_badge(); ?>
    <?php wcpt_icon('chevron-down'); ?>
  </div>

  <div class="<?php wcpt_pro_cover(); ?>">

    <div class="wcpt-editor-row-option">  
      <a class="wcpt-search__doc" href="https://wcproducttable.com/documentation/search" target="_blank">How to use →</a>
    </div>
    <!-- stopwords -->
    <div class="wcpt-editor-row-option">
      <label>
        Stopwords
        <small>
          These are generic words to be excluded during search to conserve server resource and increase result accuracy. They will be included during full keyword phrase search.<br>
          Comma separate the stopwords.
        </small>
      </label>
      <textarea wcpt-model-key="stopwords"></textarea>
    </div>

    <!-- replacements -->
    <div class="wcpt-editor-row-option">
      <label>
        Replacements
        <small>
          Correct common spelling mistakes and smartly replace keywords to increase result accuracy. Will not affect full keyword phrase search. <br>
          Enter one correction per line in this format: Correction: Incorrect 1 | Incorrect 2 ...
        </small>
      </label>
      <textarea wcpt-model-key="replacements"></textarea>
    </div>

    <!-- search by relevance label -->
    <div class="wcpt-editor-row-option">
      <label>
        'Search by relevance' label
        <small>For multiple translations enter one per line like this:</small>
        <small style="line-height: 1.5;">
          en_US: Sort by relevance<br>
          fr_FR: Trier par pertinence <br>
        </small>
      </label>
      <textarea wcpt-model-key="relevance_label"></textarea>
    </div>
    
    <?php 
      foreach( array( 'title', 'sku', 'category', 'attribute', 'tag', 'content', 'excerpt', 'custom_field' ) as $field ){
        $heading = str_replace( array('-', '_'), ' ', ucfirst( $field ) ); 
        if( $field === 'sku' ){
          $heading = 'SKU';
        }

        if( in_array( $field, array( 'attribute', 'custom_field' ) ) ){
          require 'search__common1.php';
        }else{
          require 'search__common2.php';
        }

      }
    ?>
  </div>
</div>
