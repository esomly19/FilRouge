<?php
/**
 * <h2>[ INTERFACE POUR LE LIEN CREATEUR-LISTE. ]</h2>
 *
 * <p>Le <b>comportement en interne</b> des methodes ci-dessous est <b>different</b>
 * selon si le proprietaire de la liste est identifie par le site avec un cookie
 * ( classe mywishlist\rights\IdentifyByCookies ) ou avec une variable de session
 * ( classe mywishlist\rights\Authentication ).  C'est ce qui justifie l'existence de
 * cette interface.</p>
 *
 * @author PALMIERI Adrien, VANCOILLE Victor, IANCEK Arthur, CHEVRIER Jean-Christophe.
 */
namespace mywishlist\rights;

use mywishlist\models\Liste as Liste;

interface Owner
{
    //==================================================================================================================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode permettant de "<b>sauver</b>" le lien entre le proprietaire
     * ( = createur ) de la liste nouvellement creee et la liste elle-meme.</p>
     *
     * <p>Le <b>comportement en interne</b> de cette methode est <b>different</b> selon si
     * le proprietaire de la liste est identifie par le site avec un cookie
     * ( classe mywishlist\rights\IdentifyByCookies ) ou avec une variable de session
     * ( classe mywishlist\rights\Authentication ).  C'est ce qui justifie l'existence de
     * cette interface.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question nouvellement creee.</li>
     * </ul>
     */
    public static function saveOwner(Liste $list);

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode indiquant si l'utilisateur
     * ( indentifie car il est connecte ou identifie par les cookies )
     * est proprietaire de la liste de souhaits en parametre.</p>
     *
     * <p>Le <b>comportement en interne</b> de cette methode est <b>different</b> selon si
     * le proprietaire de la liste est identifie par le site avec un cookie
     * ( classe mywishlist\rights\IdentifyByCookies ) ou avec une variable de session
     * ( classe mywishlist\rights\Authentication ).  C'est ce qui justifie l'existence de
     * cette interface.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si l'utilisateur st proprietaire de la liste.</li>
     * </ul>
     */
    public static function isOwnerOfList(Liste $list) : bool;


    //==================================================================================================================
    //==================================================================================================================
}