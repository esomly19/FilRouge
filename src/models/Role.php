<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\models;
use Illuminate\Database\Eloquent\Relations\HasMany as HasMany;
final class Role extends \Illuminate\Database\Eloquent\Model{
    //Nom de la table.
    protected $table = 'role';
    //Cle primaire de la table.
    protected $primaryKey = 'id' ;
    public $timestamps = false ;

    /**
     * <h3>[ ASSOCIATION D'ORDRE 1 -> N. ]</h3>
     *
     * <p>Methode permettant d'indiquer que la cle primaire de la table role : id
     * est utilisee en cle etrangere dans la table compte sous le nom : <b>'role_id'</b>".</p>
     *
     * <ul>
     *      <li><b>@return HasMany</b> L'association d'ordre 1 -> N.</li>
     * </ul>
     */
    public function comptes() : HasMany {
        return $this->HasMany('Super_Street_Dora_Grand_Championship_Turbo\models\Role', 'role_id') ;
    }
}