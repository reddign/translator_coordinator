<?PHP
  //Adjust the Login link depending on login status
  if(!isset($_SESSION["LoginStatus"]) || $_SESSION["LoginStatus"]!="YES"){
    $linkName = "login";
  }else{
    $linkName = "profile";
  }

  $params = $_GET;
  unset($params['lang']);
  ?>
       
    <div class="w3-top">
        <div class="w3-bar w3-white w3-padding w3-card" style="letter-spacing:4px;">
          <a href="<?PHP echo $url; ?>/index.php" class="w3-bar-item w3-button"><img src='<?PHP echo $url; ?>/images/NTlogo.png' height='50px' alt='Native Tweet'></a>
          <!-- Right-sided navbar links. Hide them on small screens -->
          <div class="w3-right">
            <a href="<?PHP echo $url; ?>/index.php" class="w3-bar-item w3-button"><?php echo t('home'); ?></a>
            <a href="<?PHP echo $url; ?>/map.php" class="w3-bar-item w3-button"><?php echo t('world_map'); ?></a>
            <a href="<?PHP echo $url; ?>/about.php" class="w3-bar-item w3-button"><?php echo t('about'); ?></a>
            <a href="<?PHP echo $url; ?>/search.php" class="w3-bar-item w3-button"><?php echo t('search'); ?></a>
            <a href="<?PHP echo $url; ?>/login.php?page=<?PHP echo $linkName; ?>" class="w3-bar-item w3-button"><?PHP echo t($linkName); ?></a>

            <form method="get" 
                  action=""
                  style = "display: inline-block; margin-left: 10px;">

                  <?php foreach ($params as $key => $value): ?>
                    <?php if (is_scalar($value)): ?>
                      <input type="hidden" name="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php endif; ?>
                  <?php endforeach; ?>

                  <select name = "lang" 
                          aria-label="Select Language"
                          onchange="this.form.submit()"
                          style = "padding: 8px; margin-top:10px;">

                    <option value="en" <?php echo $current_lang === 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="ru" <?php echo $current_lang === 'ru' ? 'selected' : ''; ?>>Русский</option>
                    <option value="uk" <?php echo $current_lang === 'uk' ? 'selected' : ''; ?>>Українська</option>
                    <option value="es" <?php echo $current_lang === 'es' ? 'selected' : ''; ?>>Español</option>
                    </option>
                  </select>
            </form>
          </div>
        </div>
      </div>
      <div class="w3-container" style="margin-top:100px">