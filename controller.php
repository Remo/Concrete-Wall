<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

/* Installer for the Lerteco Wall Package
 * copyright Lerteco
 *
 */
Loader::model('postings', 'lerteco_wall');

class LertecoWallPackage extends Package {
    protected $pkgHandle = 'lerteco_wall';
    protected $appVersionRequired = '5.4.1.1';
    protected $pkgVersion = '0.7.2';

    public function getPackageDescription() {
	return t("Adds functionality to enable a page where recent site activities are posted. Similar concept to The Facebook's wall.");
    }

    public function getPackageName() {
	return t("Concrete Wall");
    }

    public function upgrade(){
	$result = parent::upgrade();
        $this->configure();
        
	return $result;
    }

    public function install() {
	$pkg = parent::install();
        
	$this->configure();

        //should only have to install once... not on upgrades
        BlockType::installBlockTypeFromPackage('lerteco_wall', $pkg);
    }

    public function uninstall() {
	parent::uninstall();
    }

    public function configure() {
	$pkg = Package::getByHandle('lerteco_wall');

        Loader::model('single_page');

        //check if the dashboard single has already been added
        $single = Page::getByPath('/dashboard/users/lerteco_wall');
        if (! is_object($single) ||  ($single->isError() && $single->getError() == COLLECTION_NOT_FOUND)) {
            // Admin Page
            $page = SinglePage::add('/dashboard/users/lerteco_wall', $pkg);
            $page->update(array('cName'=>t('Concrete Wall')));
        }
    }

    public function postAndPossiblyRegister($uID, $data, $arrPostingType) {
        Loader::model('postings', 'lerteco_wall');

        $type = new PostingType();
        call_user_func_array(array($type, 'LoadOrUpdateOrRegister'), $arrPostingType);

        $post = new Posting();
        $post->AddWithType($type, $uID, $data);
    }

}

?>