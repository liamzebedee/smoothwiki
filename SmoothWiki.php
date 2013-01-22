<?
/**
 * SmoothWiki skin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 * @version 1.0.0
 * @author Liam Edwards-Playne [4abf.net/liam]
 */

if( !defined( 'MEDIAWIKI' ) ){
	die( "This is a skins file for MediaWiki and should not be viewed directly.\n" );
}

global $wgCachePages, $wgHtml5;
$wgCachePages = false;
$wgHtml5 = true;

class SkinSmoothWiki extends SkinTemplate {
	var $skinname = 'smoothwiki', 
		$stylename = 'smoothwiki', 
		$template = 'SmoothWikiTemplate';
	var $useHeadElement = true;
	
	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath;
		parent::initPage( $out );
		//TODO add responsive viewport	$out->addHeadItem()
		
		$out->addScriptFile( '/skins/smoothwiki/js/bootstrap.js' );
	}
	
	/**
	 * Load skin and user CSS files in the correct order
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ){
		parent::setupSkinUserCss( $out );
		
		// XXX: Cannot use addModuleStyles because the icons don't work
		$out->addStyle( 'smoothwiki/css/bootstrap.min.css' );
		$out->addStyle( 'smoothwiki/css/bootstrap-responsive.min.css' );
		$out->addStyle( 'smoothwiki/css/smoothwiki.css' );
	}
}

class SmoothWikiTemplate extends BaseTemplate {
	var $icons = array(
		'navigation' => '<i class="icon-th-list"></i>',
		'personal' => '<i class="icon-user"></i>',
		'toolbox' => '<i class="icon-wrench"></i>',
	);
	
	function main_nav() {
		$skin = $this->data['skin'];
		foreach( $this->data['sidebar'] as $key => $menu ) {
			foreach( $menu as $item ) {
				echo $this->makeListItem( $key, $item );
			}
		}
	}
	
	function user_nav() {
		$skin = $this->data['skin'];
		foreach( $this->data['personal_urls'] as $key => $item ) {
			echo $this->makeListItem( $key, $item );
		}
	}
	
	function toolbox_nav() {
		
	}
	
	function content_nav() {
		// Views as individual, actions as dropdown
		$current_individual_nav_items = 0;
		$max_individual_nav_items = 4;
		
		foreach($this->data['content_actions'] as $key => $item) :
			if ( $current_individual_nav_items == $max_individual_nav_items ) { ?>
				<?
			}
			
			echo $this->makeListItem( $key, $item );
			$current_individual_nav_items++;
		endforeach;
		
		?></ul></li><?
	}
	
		/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;

			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'toolbox', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
		}
	}

	/**
	 * @param $name string
	 * @param $content array
	 * @param $msg null|string
	 * @param $hook null|string|array
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		?>
 	<li class="nav-header" id="<?php echo Sanitizer::escapeId( "p-$name" ) ?>"<?php echo Linker::tooltip( 'p-' . $name ) ?><?php $this->html( 'userlangattributes' ) ?>><?php echo $this->icons[$msg] ?> <?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></li>
<?php
		if ( is_array( $content ) ): ?>
<?php
			foreach( $content as $key => $val ): ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			endforeach;
			if ( $hook !== null ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
<?php
		else: ?>
		<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
<?php
		endif; ?>
	
</section>
<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param $elements array
	 */
	protected function renderNavigation( $elements ) {
		$nav = $this->data['content_navigation'];
		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];
		
		global $wgVectorUseSimpleSearch;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $this->data['rtl'] ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
?>
		<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
		<?php endforeach; ?>
<?php
				break;
				case 'VARIANTS':
?>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" lang="<?php echo htmlspecialchars( $link['lang'] ) ?>" hreflang="<?php echo htmlspecialchars( $link['hreflang'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
<?php
				break;
				case 'VIEWS':
?>
		<?php foreach ( $this->data['view_urls'] as $link ): 
			if($link['id'] == 'ca-view') { continue; } // XXX: hackery ?>
			<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></li>
		<?php endforeach; ?>
<?php
				break;
				case 'ACTIONS': 
				if( count( $this->data['action_urls'] ) == 0 ) { break; } // XXX has to be better way of doing this ?>
			<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">More<span class="caret"></span></a>
					<ul class="dropdown-menu">
			<?php foreach ( $this->data['action_urls'] as $link ): ?>
					
					<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
			</ul></li>
<?php
				break;
				case 'PERSONAL':
?>
	<li class="nav-header" id="p-personal" <?php $this->html( 'userlangattributes' )?>> <?echo $this->icons['personal'] ?>User</li><?php // TODO use msg $this->msg( 'personaltools' ) ?>
	
<?php			foreach( $this->getPersonalTools() as $key => $item ) { ?>
		<?php echo $this->makeListItem( $key, $item ); ?>

<?php			} ?>

<?php
				break;
				case 'SEARCH':
?>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<?php if ( $wgVectorUseSimpleSearch && $this->getSkin()->getUser()->getOption( 'vector-simplesearch' ) ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->getSkin()->getSkinStylePath( 'images/search-rtl.png' ), 'width' => '12', 'height' => '13' ) ); ?>
			<?php endif; ?>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'text' ) ); ?>
			<?php if ( !$this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->getSkin()->getSkinStylePath( 'images/search-ltr.png' ), 'width' => '12', 'height' => '13' ) ); ?>
			<?php endif; ?>
		<?php else: ?>
		<div>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
			<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
			<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		<?php endif; ?>
			<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		</div>
	</form>
<?php

				break;
			}
		}
	}
	
	public function execute() {
		global $wgRequest;
		$skin = $this->data['skin'];
		require_once( "smoothwiki/page.php" );
	}
}
?>
