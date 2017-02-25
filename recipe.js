$(function(){
  $( "#sortable" ).sortable({
    axis: 'y',
    update: function(event, ui) {
      var data = $(this).sortable('serialize');
      // console.log(data);
      $.ajax({
        data: data,
        type: 'POST',
        url: document.URL,
        success: function(data) {
          var order = 1;
          $('.order_display').each(function(){
            $(this).html(order++);
          });          
        }
      });
    }
  });
  $( "#sortable" ).disableSelection();

  $(".remove_instruction").on('click', function(){
    $id = $(this).data('id');
    $op = $(this).data('op');
    var data = 'id=' + $id + '&op=' + $op;
    $(this).parent().remove();
    $.ajax({
      url: document.URL,
      data: data,
      type: 'DELETE',
      success: function(data){
        var order = 1;
        $('.order_display').each(function(){
          $(this).html(order++);
        });          
      }
    });
  });

  $(".remove_recipe").on('click', function(){
    $id = $(this).data('id');
    var data='id=' + $id;
    $(this).parent().remove();
    $.ajax({
      url: document.URL,
      data: data,
      type: 'DELETE',
      success: function(data) {
        // alert('deleted' + data);
      }
    })
  });



});