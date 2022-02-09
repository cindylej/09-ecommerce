<?php 
// FONCTION INTERNAUTE AUTHENTIFIE 
function connect()
{
    // Si l'indice 'user' n'est pas définit dans la session, cela veut dire que l'internaute n'est pas passé par la page connexion, donc n'est pas authentifié sur le site
    if(!isset($_SESSION['user']))
    {
        return false;
    }
    else // Sinon l'indice 'user' est définit dans le session, l'internaute est passé par la page connexion et est authentifié sur le site
    {
        return true;
    }
}

// FONCTION INTERNAUTE AUTHENTIFUE ET ADMINISTATEUR DU SITE 

function adminConnect()
{
    if (connect() && $_SESSION ['user']['statut'] == 'admin') 
    {
       return true;
    }
    else
    {
        return false;
    }
}