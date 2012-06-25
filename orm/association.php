<?php
/**
 * Fichier association.php
 *
 * LICENCE
 *     You are free:
 *       to Share - to copy, distribute and transmit the work
 *       to Remix - to adapt the work
 *     Under the following conditions:
 *       Attribution - You must attribute the work in the manner specified by the author or licensor 
 *       (but not in any way that suggests that they endorse you or your use of the work).
 *       Just keep this header.
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @version    $Id:$
 * @link       http://www.wamania.com
 * @since      File available since Release 0.1
*/

/**
 * Classe de factory d'associations
 * 
 * Utilisée pour construire les objets associations en fonction du type de
 * l'association.
 * Chaque association hérite de cette classe et partage les attributs communs ainsi 
 * qu'une fonction push qui permet d'ajouter un objet à l'association sans ce soucier
 * du type de l'association
 *
 *
 * @copyright  2008 Wamania.com
 * @license    http://creativecommons.org/licenses/by/2.0/fr/
 * @package    Lithium
 * @subpackage ORM
 * @version    Release: @package_version@
 * @link       http://www.wamania.com
 * @since      Class available since Release 0.1
 */
abstract class LiAssociationAbstract extends LiSqlQuery 
{
    /**
     * Contient les info de l'association
     * @var Array
     */
    protected $assoc;
    
    /**
     * L'object parent
     * @var Object of LiModel
     */
    protected $parent;
    
    /**
     * Constructeur par defaut
     * @return Void
     * @param object of LiModel $parent
     * @param Array $assoc
     */
    public function __construct($parent, $assoc) 
    {
        $this->parent = $parent;
        $this->assoc = $assoc;
        
        parent::__construct();
    }
    
    /**
     * Factory d'association en fonction du type de celle-ci
     * @return Object
     * @param object of LiModel $parent
     * @param Array $assoc
     */
    public static function load($parent, $assoc) 
    {
        switch($assoc['type']) {
            
            case 'hasmany':
                return LiAssociationHasMany::load($parent, $assoc);
                break;
                
            case 'manytomany':
                return LiAssociationManyToMany::load($parent, $assoc);
                break;
                
            case 'belongsto':
                return LiAssociationBelongsTo::load($parent, $assoc);
                break;
                
            case 'hasone':
                return LiAssociationHasOne::load($parent, $assoc);
                break;
            default:
                throw new LiException ('Association inconnue');
                break;
        }
    }
    
    /**
     * Recharge le contenu de l'association
     * 
     * @return void
     */
    public function reload() 
    {
        $this->execute();
    }
    
    /**
     * Méthode universelle pour ajouter un élément à l'association
     *
     * @param LiModel $child
     */
    public function push(LiModel $child) 
    {
        if ( ($this->assoc['type'] == 'hasmany') || ($this->assoc['type'] == 'manytomany') ) {
            $this->data_add[] = $child;
        
        } elseif ( ($this->assoc['type'] == 'belongsto') || ($this->assoc['type'] == 'hasone') ) {
            $this->results[0] = $child;
        }
    }
}