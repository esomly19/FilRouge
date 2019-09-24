<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\models;
use Illuminate\Database\Eloquent\Relations\HasMany as HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsTo;
final class Compte extends \Illuminate\Database\Eloquent\Model{
    //Nom de la table.
    protected $table = 'compte';
    //Cle primaire de la table.
    protected $primaryKey = 'no' ;
    public $timestamps = false ;

    /**
     * <h3>[ ASSOCIATION D'ORDRE 1 -> N. ]</h3>
     *
     * <p>Methode permettant d'indiquer que la cle primaire de la table compte : no
     * est utilisee en cle etrangere dans la table liste sous le nom : <b>'user_id'</b>".</p>
     *
     * <ul>
     *      <li><b>@return HasMany</b> L'association d'ordre 1 -> N.</li>
     * </ul>
     */
    public function listes() : HasMany {
        return $this->HasMany('Super_Street_Dora_Grand_Championship_Turbo\models\Liste', 'user_id') ;
    }

    /**
     * <h3>[ ASSOCIATION D'ORDRE N -> 1. ]</h3>
     *
     * <p>Methode permettant d'indiquer l'existence d'une cle etrangere 'role_id' dans
     * la table compte, faisant reference a le cle primaire 'id' de la table
     * role.</p>
     *
     * <ul>
     *      <li><b>@return BelongsTo</b> L'association d'ordre N -> 1.</li>
     * </ul>
     */
    public function role() : BelongsTo {
        return $this->BelongsTo('Super_Street_Dora_Grand_Championship_Turbo\models\Role', 'role_id') ;
    }
}