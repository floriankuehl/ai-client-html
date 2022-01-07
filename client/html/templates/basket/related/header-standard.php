<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */

$enc = $this->encoder();


?>
<link rel="stylesheet" href="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/basket-related.css', 'fs-theme' ) ) ?>">
<script defer src="<?= $enc->attr( $this->content( $this->get( 'contextSite', 'default' ) . '/basket-related.js', 'fs-theme' ) ) ?>"></script>

<?= $this->get( 'relatedHeader' ) ?>
