<?PHP
  //Adjust the Login link depending on login status
  if(!isset($_SESSION["LoginStatus"]) || $_SESSION["LoginStatus"]!="YES"){
    $linkName = "Login";
  }else{
    $linkName = "Profile";
  }

?>        
    <div class="w3-top">
        <div class="w3-bar w3-white w3-padding w3-card" style="letter-spacing:4px;">
          <a href="<?PHP echo $url; ?>/index.php" class="w3-bar-item w3-button"><img src='<?PHP echo $url; ?>/images/logo.png' height='50px'></a>
          <!-- Right-sided navbar links. Hide them on small screens -->
          <div class="w3-right w3-hide-small">
            <a href="<?PHP echo $url; ?>/about.php" class="w3-bar-item w3-button">About</a>
            <a href="<?PHP echo $url; ?>/search.php" class="w3-bar-item w3-button">Search</a>
            <a href="<?PHP echo $url; ?>/login.php?page=<?PHP echo strtolower($linkName); ?>" class="w3-bar-item w3-button"><?PHP echo $linkName; ?></a>
          </div>
        </div>
      </div>
      <div class="w3-container" style="margin-top:100px">