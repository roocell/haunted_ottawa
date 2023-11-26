<?php
$oath2_client_id = "";
$oath2_client_secret = "";

?>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js"></script>

<meta name="google-signin-client_id" content=<?php echo $oath2_client_id; ?>>

<style>
.googlelogin
{
  position: absolute;
  top: 10px;
  left: 180px;
  font-size: 12px;
}
</style>


<div class=googlelogin >

<!-- sign in button -->
<div id=signinbutton class="g-signin2" data-onsuccess="onSignIn"></div>

<!-- sign out button -->
<a id=signoutlink href="#" onclick="signOut();" style="display:none;">Sign out</a>


<!-- username display or form to create username -->
<div id="usernamefield">
  <?php
  if (isset($g_user))
  {
    echo $g_user['username'];
  }
  ?>
</div>
<div id="usernameform" style="display: none;">
  <!-- empty for now, JS will populate it -->
  <form onSubmit="usernameFormSubmit(this); return false;">
    Choose a display name for leaderboards<BR>
     <input type=text name=username id=usernameinput> </input>
     <input type=submit></input>
  </form>
</div>
</div>


<script>
  function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
      document.getElementById('cluediv').style = "display: none;";
      document.getElementById('usernameform').style = "display: none;";
      document.getElementById('usernamefield').innerHTML = "";

      document.getElementById('signoutlink').style = "display: none;";
      document.getElementById('signinbutton').style = "display: block;";

    });
  }
</script>


<script>
var g_googleid = "";
var g_userlevel = "";
function onSignIn(googleUser) {
  document.getElementById('usernameform').style = "display: none;";

  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.

  g_googleid = profile.getId();
  getUser();
  document.getElementById('signoutlink').style = "display: block;";
  document.getElementById('signinbutton').style = "display: none;";

}

function activateLevel (userdata)
{
  g_userlevel = userdata.result[0].level;
  document.getElementById('usernamefield').innerHTML = userdata.result[0].username;
  document.getElementById('cluediv').style = "display: block;";
}

function getUser()
{
  // check if user exists.
  jQuery.ajax({
      type: "POST",
      url: 'dbajax.php',
      dataType: 'json',
      data: {functionname: 'getUserAjax', arguments: [g_googleid]}
      })
      .done(function (data) {
                    console.log("worked");
                    console.log(data);

                    // if exists - display username next to google button
                    if (data.result.length > 0)
                    {
                      var url=window.location.href;
                      console.log(url);
                      if (url.includes("udid"))
                      {
                        activateLevel(data);
                      } else {
                        var newurl = window.location.href+"?udid="+data.result[0].udid;
                        console.log("redirect to: "+newurl);

                        // redirect to same page with $udid arguments
                        window.location.href = newurl;
                      }
                    } else {
                      // if not - ask for a username in a form (JS)
                      document.getElementById('usernameform').style = "display: block;";

                    }

              })
      .fail(function (data) {
              console.log("fail");
              console.log(data);
            });

}

function usernameFormSubmit(element)
{
  if (element.username.value.length == 0)
  {
    alert("Please enter a display name for the leaderboard.");
    return false;
  }

  console.log("submit username " + g_googleid + " " + element.username.value);
  // form return -> create a new user
  jQuery.ajax({
      type: "POST",
      url: 'dbajax.php',
      dataType: 'json',
      data: {functionname: 'insertUserAjax', arguments: [g_googleid, element.username.value]}
    })
      .done(function (obj, textstatus) {
                if( !('error' in obj) ) {
                    yourVariable = obj.result;
                    console.log(obj);

                    // hide the form and display the username
                    document.getElementById('usernameform').style = "display: none;";
                    document.getElementById('cluediv').style = "display: none;";
                    getUser();

                }
                else {
                    console.log(obj.error);
                }
              })
      .fail(function (data) {
              console.log(data);
            })
      .always(function (data) {
            });
}

</script>
