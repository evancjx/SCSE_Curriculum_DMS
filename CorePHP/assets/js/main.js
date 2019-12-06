
$(document)
.on("submit", "form.user", function(event){
  event.preventDefault();

  var _form = $(this);
  var _error = $(".js-error", _form);

  var dataObj = {
    email: $("input[type='email']", _form).val(),
    password: $("input[type='password']", _form).val()
  }

  if(dataObj.email.length < 6){
    _error.text("Please enter a valid email address").show();
    return false;
  }
  else if (dataObj.password.length < 8){
    _error.text("Please enter a passphrase that is at least 8 characters long").show();
    return false;
  }

  _error.hide();

  $.ajax({
    type:'POST',
    url: (_form.hasClass('js-login') ? '/ajax/login.php' : '/ajax/register.php'),
    data:dataObj,
    dataType:'json',
    async: true,
  })
  .done(function ajaxDone(data){
    console.log(data);
    if(data.error !== undefined){
      _error.html(data.error).show();
    }
    else if(data.redirect !== undefined){
      window.location = data.redirect;
    }
  })
  .fail(function ajaxFailed(e){
    console.log(e);
  })
  .always(function ajaxAlwaysDoThis(data){
    console.log('Always');
  })

  return false;
})
.on('click', '.LO_remove', function(){
})
