(function($, model){

  model.parent;

  // settser and getter
  model.get_data = function(){

    return JSON.parse( JSON.stringify( this.data ) );
  }

  model.set_data = function(data){
    this.ensure_data_ids(data);
    this.data = $.extend([], data);
    this.parent.view.render();
    this.parent.$elm.data('wcpt-data', this.data);
    this.parent.$elm.trigger('change');
  }

  model.ensure_data_ids = function(data){
    if( ! data ){
      return;
    }

    var timestamp = Date.now();
    for( var row_index= 0; row_index < data.length; row_index++ ){      
      var row = data[row_index];

      if( ! row.id ){
        row.id = timestamp++;
      }

      if( ! row.type ){
        row.type = 'row';
      }

      for( var elm_index= 0; elm_index < row.elements.length; elm_index++ ){
        var element = row.elements[elm_index];
        if( ! element.id ){
          element.id = timestamp++;
        }
      }

    }
  }

  model.get_export_code = function( row_index, elm_index ){
    if( ! row_index && ! elm_index ){
      var element = this.data;
    }else{
      var element = this.data[row_index].elements[elm_index];
    }

    return JSON.stringify( this.strip_id( element ) );
  }

  model.strip_id = function(item){
    var model = this

    $.each( item, function( key, val ){
      if( typeof val == 'object' ){
        item[key] = model.strip_id(val);
      }

      if( key == 'id' ){
        delete item[key];
      }
    } )
    return item;
  }

  model.random_id = function(item){
    var model = this

    $.each( item, function( key, val ){
      if( typeof val == 'object' ){
        item[key] = model.random_id(val);
      }

      if( key == 'id' ){
        item[key] = Math.round( Math.random() * 100000000 );
      }
    } )
    return item;
  }

  model.get_row = function(id){
    id = parseInt(id);
    for( var i = 0; i < this.data.length; i++ ){
      var row = this.data[i];
      if( row.id == id )  return row;
    }
  }

  model.get_element = function(id){
    id = parseInt(id);
    for( var i = 0; i < this.data.length; i++ ){
      var row = this.data[i];
      for( var ii = 0; ii < this.data[i].elements.length; ii++ ){
        var element = this.data[i].elements[ii];
        if( element.id == id )  return element;
      }

    }
  }

  model.add_row = function(row, row_index){

    if( ! row ){
      row = {elements:[], id: Date.now(), condition: {}, style: {}};
    }

    var data = this.get_data();

    if( ! row_index ){
      row_index = data.length;
    }

    data.splice( row_index, 0, row );
    this.set_data(data);
  }

  model.add_element = function(element, row_index, element_index){

    // ensure single row at least
    var data = this.get_data();
    if( ! data.length ){
      this.add_row();
    }

    var data = this.get_data();
    if( undefined == row_index ){
      row_index = data.length - 1; // at last row
      var row = data[row_index];
    }

    if( undefined == element_index ){
      element_index = row.elements.length
    }

    data[row_index].elements.splice( element_index, 0, element );

    this.set_data(data);
  }

  model.remove_element = function( row_index, element_index ){

    var data = this.get_data(),
        row = data[row_index];

    row.elements.splice( element_index, 1 );
    this.set_data(data);
  }

  model.update_element = function( new_data, row_index, element_index ){

    var data = this.get_data();

    data[row_index].elements.splice( element_index, 1, new_data );
    this.set_data(data);
  }

  model.duplicate_element = function( row_index, element_index ){

    var data = this.get_data(),
        row = data[row_index];

    row.elements.splice( element_index + 1, 0, $.extend( true, {}, row.elements[element_index] ) );
    this.random_id( row.elements[element_index + 1] );
    row.elements[element_index + 1].id = Date.now();
    this.set_data(data);
  }

  model.remove_row = function( row_index ){

    var data = this.get_data();
    data.splice( row_index, 1 );
    this.set_data(data);
  }

  model.update_row = function( new_data, row_index ){

    var data = this.get_data();
    data.splice( row_index, 1, new_data );
    this.set_data(data);
  }

  model.duplicate_row = function( row_index ){

    var data = this.get_data();
    data.splice( row_index + 1, 0, $.extend( true, {}, data[row_index] ) );
    this.random_id( data[row_index + 1] );
    this.set_data(data);
  }

  model.data = null;

})( jQuery, WCPT_Block_Editor.Model );
