document.addEventListener('DOMContentLoaded', function () {
    var d=document.getElementById ("delete_contact");

    d.addEventListener('click', function(){
        delete_function ();
    });

    function delete_function (){
        var flag = confirm ("Do you want to delete the contact?");
        if (flag == true){
            var id = d.getAttribute("data-id")
            window.location.href = "delete.php?id="+id;
        }
    }
});
