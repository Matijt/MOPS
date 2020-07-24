/**
 * Created by Matias on 14/07/2017.
 */
$(function () {
    $(".modalButtonView").addClass('btn btn-info');
    $(".modalButtonEdit").addClass('btn btn-warning');
    $(".modalButtonDelete").addClass('btn btn-danger');
    $(".modalButtonCreate").addClass('btn btn-success');
    // get the click event of the view button
    $(".modalButtonView").click(function(){
        $('#edit').modal('show').find('#editContent').load($(this).attr('value'));
//        alert("view");
    });

    // get the click event of the edit button
    $(".modalButtonEdit").click(function(){
        $('#edit').modal('show').find('#editContent').load($(this).attr('value'));
//        alert("edit");
    });

    // get the click event of the create button
    $(".modalButtonCreate").click(function(){
        $('#edit').modal('show').find('#editContent').load($(this).attr('value'));
//        alert("create");
    });
});
