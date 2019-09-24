<?php
/**
 * <h2>[ CLASSE POUR L'IDENTIFICATION AVEC COOKIES. ]</h2>
 *
 * <p>Note : pour la gestion des cookies, nous avons considere que les utiliser
 * sans les methodes proposees par Slim etait plus simple.</p>
 *
 * <p>Ceci est une classe concrete "<b>sterile</b>", c-a-d qu'aucune classe ne peut
 * en heriter ( d'ou la presence du final devant class).</p>
 *
 * @author PALMIERI Adrien, VANCOILLE Victor, IANCEK Arthur, CHEVRIER Jean-Christophe.
 */
namespace mywishlist\rights;
use mywishlist\models\Liste as Liste;
final class IdentifyByCookies implements Owner {
    //==================================================================================================================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode permettant de "<b>sauver</b>" le lien entre le proprietaire
     * ( = createur ) de la liste nouvellement creee et la liste elle-meme.<p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question nouvellement creee.</li>
     * </ul>
     */
    public static function saveOwner(Liste $list){
        // Le cookie expire quand la liste expire.
        $time =
            (
                time()
                +
                // La methode strtotime(...) rerourne le timestamp Unix
                // (le nombre de secondes depuis le 1er Janvier 1970)
                strtotime($list->expiration)
                -
                // La fonction date(...) retourne la date actuelle.
                // On souhaite recuperer la date actuelle au format de
                // la date d'expiration de la liste soit 'YYYY-MM-DD' en mysql
                // (format anglais des dates), or ce format s'ecrit differemment
                // en parametre de date(...), il s'ecrit ainsi :
                // Y = format annee avec 4 chiffres.
                // m = format mois : 01-12.
                // d = format jour : 01-31.
                strtotime(date('Y-m-d'))
            );

        // Un cookie retient que le client sur ce navigateur est le proprietaire de la liste.
        setcookie("token-$list->no", $list->token, $time);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode indiquant si l'utilisateur
     * ( identifie par les cookies )
     * est proprietaire de la liste de souhaits en parametre.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si l'utilisateur st proprietaire de la liste.</li>
     * </ul>
     */
    public static function isOwnerOfList(Liste $list) : bool{
        return (
                 isset($_COOKIE["token-$list->no"])
                 and
                 $_COOKIE["token-$list->no"] === $list->token
               );
    }


    //==================================================================================================================
    //==================================================================================================================
}