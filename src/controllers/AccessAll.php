<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\controllers;
interface AccessAll
{
    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Cette methode a un <b>comportement en interne different</b>
     * selon si on est dans la classe mywishlist\controllers\ParticipantController
     * ou si on est dans la classe mywishlist\controllers\CreatorController. Cela
     * justifie l'existence de cette interface.</p>
     *
     * <p>En effet dans la classe mywishlist\controllers\ParticipantController,
     * le participant peut acceder a la liste des listes <b>publiques</b>, tandis
     * que dans mywishlist\controllers\CreatorController, le createur
     * <b>authentifie</b> peut acceder a la liste de <b>ses</b> listes.</p>
     *
     * <p>Pour eviter des chargements trop longs, la liste des listes est
     * affichee par un ensemble de pages presentant chacune 5 listes de souhaits.</p>
     *
     * <ul>
     *    <li><b>@param int $numPage</b>    Le numero de la page.</li>
     * </ul>
     */
    public static function findAll(int $numPage);
}