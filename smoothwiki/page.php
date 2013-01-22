<?php $this->html( 'headelement' ); ?>

<div class="container-fluid" id="container">
	<div class="row-fluid" id="bulk">
	<aside id="sidebar" class="span2 well well-small">
		<h1 id="brand"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>"><? echo $GLOBALS['wgSitename'] ?></a></h1>
		
		<nav class="nav nav-list">
			<?php $this->renderNavigation( 'PERSONAL' ); ?>
			<?php $this->renderPortals( $this->data['sidebar'] ); ?>
		</nav>
	</aside>
	
	<article id="content" class="span10">
		<header>
			<? if ( $this->data['sitenotice'] != null ) : ?>
			<div id="siteNotice" class="well"><?php $this->html( 'sitenotice' ) ?></div>
			<? endif; ?>
			
			<nav id="content-nav" class="nav nav-tabs">
				<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS' ) ); ?>
				<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS', /*'SEARCH'*/ ) ); ?>
				
				<!--<li class="input-append pull-right">
					<input class="span3" id="appendedInputButtons" type="text" placeholder="Search">
	  				<button class="btn" type="button">Go</button>
	  				<button class="btn" type="button">Search</button>
				</li>-->
			</nav>
		</header>
		
		<article>
			<a id="top"></a>
			<h1 id="firstHeading" class="firstHeading"><?php $this->html( 'title' ) ?></h1>
			<?php $this->html( 'bodycontent' ) ?>
			<?php if( $this->data['catlinks'] ) { $this->html('catlinks'); } ?>
		</article>
	</article>
	</div>
	
	<footer <?php $this->html( 'userlangattributes' ) ?> class="row-fluid muted credit">
		<?php
		$details = array( 
			'about', 'copyright', 'credits',
			'privacy', 'disclaimer', );
		?>
	
		<p id="lastMod"><? echo $this->data['lastmod'] ?></p>
	
		<ul><?php
			foreach ( $details as $detail ):
				if ( empty( $this->data[$detail] )) { continue; } ?>
				<li id="<?php echo $detail ?>"><?php $this->html( $detail ) ?></li>
				<li class="seperator"></li>
			<?php endforeach; ?>
		</ul>
		
		<?php
		if ( $this->data['poweredbyico'] ) : ?>
		<div id="f-poweredbyico"><?php $this->html('poweredbyico') ?></div>
		<?php endif;
	
		if ( $this->data['copyrightico'] ) : ?>
		<div id="f-copyrightico"><?php $this->html('copyrightico') ?></div>
		<?php endif; ?>
	</footer>

</div>

<?php $this->printTrail(); ?>
</body>
</html>
