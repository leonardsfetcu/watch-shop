$(document).ready(
	function()
	{

		$(".data-container input, .data-container textarea").prop("disabled",true);

		$("button[name='edit-account-data']").click(
			function()
			{
				$(".data-container input, .data-container textarea").prop("disabled",false);
			});

		$("button[name='save-account-data']").click(
			function()
			{
				_firstname = $("input[name='firstname']").val();
				_lastname = $("input[name='lastname']").val();
				_birthdate = $("input[name='birthdate']").val();
				_email = $("input[name='email']").val();
				_address = $("textarea").val();
				_phone = $("input[name='phone']").val();
				_currentPassword = $("input[name='current-password']").val();
				_newPassword = $("input[name='new-password']").val();
				_newPassword2 = $("input[name='new-password2']").val();
				_username = $("span#username").text();
				
				$.ajax(
				{
					type:"POST",
					data:
					{
						firstname: _firstname,
						lastname: _lastname,
						birthdate: _birthdate,
						email: _email,
						phone: _phone,
						address: _address,
						currentPassword: _currentPassword,
						newPassword: _newPassword,
						newPassword2: _newPassword2,
						username: _username
					},
					url: "account-details.php",
					success: 
						function(result)
						{
							try{
								error=false;
								result = JSON.parse(result);
								if(result.hasOwnProperty('query'))
								{
									alert(result.query);
									error=true;
								}
								if(result.hasOwnProperty('email'))
								{
									alert(result.email);
									error=true;
								}
								if(result.hasOwnProperty('currentPassword'))
								{
									alert(result.currentPassword);
									error=true;
								}
								if(result.hasOwnProperty('newPassword'))
								{
									alert(result.newPassword);
									error=true;
								}

								$("h4#session-firstname-lastname").text(_firstname+" "+_lastname);
								$("input[name='new-password'], input[name='new-password2'], input[name='current-password']").val("");
								$(".data-container input, .data-container textarea").prop("disabled",true);
								if(error==false)
									alert("Your account was successfully updated!");
							}
							catch(e)
							{
								alert(e);
							}
						}
				});

				

			});


	});
