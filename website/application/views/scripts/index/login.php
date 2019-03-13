<form method="post" action="">
    <label for="access_name">Identifiant : </label>
    <input type="text" value="" name="access_name" /><br />
 
    <label for="access_cred">Mot de passe : </label>
    <input type="password" value="" name="access_cred" /><br /> 

<?php if ($this->logError) : ?>  

    <p><span class="error">Erreur de combinaison login / mot de passe.</span></p>

<?php endif; ?>

    <input type="submit" name="submitted" value="Me connecter" />
</form>
