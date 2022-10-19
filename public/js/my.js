$(document).ready(function(){

   getUsers();
   $('#sendForm').on('submit',getToken);
   $('#showMoreUsers').on('click', getMoreUsers);
   $('#showAllUsers').on('click', getAllUsers);

   function getAllUsers(event)
   {
       event.preventDefault();
       let urlShowAllUsers = $('#showAllUsers').attr('href');
       $('tbody tr').remove();
       $('#showMoreUsers').remove();
       $('table').after('<a id="showMoreUsers" href="" class="btn btn-sm btn-warning btn-min-width border-secondary text-bold ">Show more >> </a>')
       $('#showMoreUsers').attr('href', urlShowAllUsers);
       $('#showMoreUsers').on('click', getMoreUsers);
       getUsers();
   }

    function getUsers()
    {
        let urlShowAllUsers = $('#showMoreUsers').attr('href');

        $.getJSON(urlShowAllUsers, function (data){
            let nextUrl = data.links.next_url;
            $('#showMoreUsers').attr('href', nextUrl);
            for (let user of data.users) {
                $('#showAllUsersTable').append(
                    '<tr><td>'+ user.id +'</td>' +
                    '<td>'+ user.name +'</td>\n' +
                    '<td>'+ user.email +'</td>\n' +
                    '<td>'+ user.phone +'</td>\n' +
                    '<td>'+ user.position +'</td>\n' +
                    '<td><img src="'+ user.photo +'" alt="'+ user.name +'"></td>\n' +
                    '</tr>');
            }
            if(!nextUrl) {
                $('#showMoreUsers').remove();
            }
        });
    }

   function getMoreUsers(event)
   {
       event.preventDefault();
       getUsers();
   }

    function getToken(event)
    {
        event.preventDefault();
        let dataForm = new FormData(this);
        let urlToken = $('#urlGetToken').attr('value');
        let urlPost = $(this).attr('action');
        $.getJSON(urlToken, function (data){
            sendForm(urlPost, data.token, dataForm);
        });
    }

    function sendForm(urlPost, token, dataForm)
    {
        let url = urlPost;
        $('#name').removeClass('border-danger');
        $('#name_error').remove();
        $('#email').removeClass('border-danger');
        $('#email_error').remove();
        $('#position_id').removeClass('border-danger');
        $('#position_id_error').remove();
        $('#phone').removeClass('border-danger');
        $('#phone_error').remove();
        $('#photo').removeClass('border-danger');
        $('#photo_error').remove();
        $('#other_error').text("");
        $.ajax({
            headers: {
                'Token': token
            },
            url: url,
            method: 'POST',
            data:  dataForm,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function()
            {
                $('#resultModal').modal('show');
                $('#sendForm').trigger("reset");
                $('#addUserModal').modal('hide');
            },
            error: function (response) {
                if(response.status === 422) {
                    for (let [error, message] of Object.entries(response.responseJSON.fails)) {
                        $('#'+ error).after('<div id="' + error + '_error" ><span class="text-danger"><strong>'
                            + message + '</strong></span>');
                        $('#'+ error).addClass('border-danger');
                    }
                }  else {
                    $('#other_error').text("Error: " + response.responseJSON.message).addClass('text-danger');
                }
            }
        });
    }
});
