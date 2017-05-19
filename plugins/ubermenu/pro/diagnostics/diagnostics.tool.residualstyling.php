<div class="umd-tool umd-tool-residual-styling">

	<h1 class="umd-tool-toggle">Residual Styling Detection / Manual Integration Tool <i class="fa fa-minus"></i><i class="fa fa-plus"></i></h1>

	<div class="umd-tool-body">
		<div class="umd-tool-steps">

			<div class="umd-tool-step">
				<h3>Overview</h3>
				<p><a target="_blank" href="http://sevenspark.com/docs/ubermenu-3/integration/residual-styling">Residual styling</a> is when CSS styles from your theme interfere with UberMenu's styles.  This is most commonly caused by CSS applied based on an element wrapping UberMenu.</p>
				<p>This tool allows you to determine the source element of your residual styling, by removing containers surrounding the menu until the residual styling has disappeared from the menu.  The tool will then tell you where to look in your theme files to find the code that needs to be replaced to eliminate the residual styling permanently.</p>

				<p>Please visit this Knowledgebase article for a <a target="_blank" href="http://sevenspark.com/docs/ubermenu-3/diagnostics/residual-styling-detection-tool" target="_blank">Walkthrough of the Residual Styling Detection / Manual Integration Tool</a></p>

				<p>Before beginning, check if there is an <a href="http://sevenspark.com/docs/ubermenu-3/theme-integration" target="_blank">Integration Guide</a> specifically for your theme, to save you some time</p>

				<a href="#" class="umd-tool-step-btn umd-tool-step-next">Next</a>
			</div>

			<div class="umd-tool-step">
				<h3>Step 1: Activate Direct Injection</h3>

				<p>Enable the Direct Injection setting in the UberMenu Control Panel and assign your menu to it.</p>

				<p>This will add an extra menu at the top of your site (you will disable it at the end of this process).  If the source of the residual styling is from a container/wrapper on the menu, this menu will not be affected, so it will serve as a "control" (if this menu is also broken, it means the theme or another plugin may be interfering in a different way.  Please see <a target="_blank" href="http://sevenspark.com/docs/ubermenu-3/integration/theme-interference" title="Permalink to Theme Interference" rel="bookmark">Theme Interference</a> for more information).  By comparing the styles of this menu to your main menu, you can tell when the source of the issue has been removed.</p>

				<a href="#" class="umd-tool-step-btn umd-tool-step-next">Next</a>
				<a href="#" class="umd-tool-step-btn umd-tool-step-prev">Back</a>
				
			</div>

			<div class="umd-tool-step">
				<h3>Step 2: Unwrap the menu</h3>

				<p>Click the "Remove Container" button to remove the menu's parent wrapper.  If there is still residual styling from the theme, continue to click "Remove Container" until it is removed.  After each click, check the menu to see if the issue has been resolved.  Once the styles look correct, click the "Residual Styling is Removed" button.</p>

				<div class="umd-tool-rs-multiple-menus">
					<p>Multiple menus detected.  Please click on the green border surrounding the menu you wish to work with.</p>
				</div>

				<div class="umd-tool-rs-menu-chosen">
					<h6>Removed Containers:</h6>
					<p class="umd-tool-rs-unwrapped-element"></p>

					<a href="#" class="umd-tool-action-btn" id="umd-tool-rs-unwrap"><i class="fa fa-wrench"></i> Remove Container</a>
					<a href="#" class="umd-tool-action-btn" id="umd-tool-rs-resolved"><i class="fa fa-check-circle"></i> Click once Residual Styling is Removed</a>
					<a href="#" class="umd-tool-step-btn umd-tool-step-prev">Back</a>

					<p>If your styles never end up looking correct, the source of the residual styling my be overly generic and not be able to be eliminated by removing a container (try using Chrome Developer Tools to source the issue instead), or the interference may not be from CSS - please see <a href="http://sevenspark.com/docs/ubermenu-3/integration/theme-interference" title="Permalink to Theme Interference" rel="bookmark">Theme Interference</a> for more information.</p>
				</div>

			</div>

			<div class="umd-tool-step">
				<h3>Step 3: Find the code to replace in your theme</h3>

				<p>The containers surrounding your menu look something like this:</p>

				<p><code id="umd-tool-rs-original-wrappers"></code></p>

				<p>So your conditional replacement will look something like this:</p>

				<p><code id="umd-tool-rs-new-wrappers"></code></p>

				<p class="umd-tool-warning"><i class="fa fa-warning"></i> (Do not copy and paste this code, it is just an approximation based on the front end markup, while you will be replacing back end PHP)</p>

				<p>Click below to search for the theme's menu code within your theme files</p>

				<?php if( current_user_can( 'edit_files' ) ): ?>
				<a class="umd-tool-action-btn" id="umd-tool-rs-search" href="#" data-nonce="<?php echo wp_create_nonce( 'ubermenu_theme_search' ); ?>">Search theme files for menu</a>
				<?php endif; ?>

				<a href="#" class="umd-tool-step-btn umd-tool-step-prev">Back</a>
			</div>

			<div class="umd-tool-step">
				<h3>Search Results</h3>
				<p>Here are the results, showing files and line numbers from your theme where you can look for the appropriate code to replace. Remember, you want to replace the entire block starting with</p>

				<p><code id="umd-tools-rs-code-replace-reminder"></code></p>

				<p>Results are also shown for wp_nav_menu() in case the container element can't be located, but it's important to note that if you just replace the wp_nav_menu() function, manual integration won't do anything</p>

				<div id="umd-tools-rs-search-results">Search results loading...</div>

				<div class="umd-tools-rs-manual-integration-skeleton">

					<h3>What to do next</h3>
					
					<p>Now that you have the potential locations for the code in the theme, look through those locations in your theme files to locate the correct code block.  If you had an ID hit in your results, that's likely the exact spot.  The point is to replace the theme's entire block of menu code with UberMenu, effectively moving UberMenu outside of the theme's menu container.  Remember, it'll look something like (but not exactly like) this:</p>

					<p><code id="umd-tools-rs-code-replace-reminder-full"></code></p>

					<p>Once you find the right location (hint: 95% of the time, it's in header.php), find the relevant block of code that creates the menu (the one that looks similar to the code above), which will likely be multiple lines.  This is the <strong>Theme Menu Code</strong></p>

					<p>We will now write a bit of PHP that says "If UberMenu is installed, show UberMenu, otherwise, show the theme menu".  This is the skeleton for that logic.  You will replace the 2 parts that start with "//" with two pieces of code - the part in the IF statement is generated in the Control Panel > Manual Integration by selecting the appropriate theme location and copying and pasting.  The ELSE statement will containt the block of <strong>Theme Menu Code</strong></p>

<p><code><?php echo esc_html( "<?php if( function_exists( 'ubermenu' ) ): ?>
	//UBERMENU MANUAL INTEGRATION CODE GOES HERE - ubermenu()
<?php else: ?>
	//THEME MENU CODE, AS IS, GOES HERE
<?php endif; ?>" ); ?>
</code></p>

					<p>Make sure not to break the PHP code opening and closing tags when adding this code</p>

					<p>Here is an EXAMPLE (do not copy this code) of a situation where the interference was coming from the class "main-navigation"</p>

<p><code><?php echo esc_html( "<?php if( function_exists( 'ubermenu' ) ): ?>
	<!-- This is the UberMenu Manual Integration code, generated from the Control Panel -->
	<?php ubermenu( 'main' , array( 'theme_location' => 'primary' ) ); ?>
<?php else: ?>
	<!-- This was the theme's code -->
	<nav class='main-navigation'>
		<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
	</nav>
<?php endif; ?>" ); ?>
</code></p>

				<p>Keep in mind that you'll want to make the final change in a <a href="https://codex.wordpress.org/Child_Themes">Child Theme</a> to preserve your changes, so that they won't be overwritten next time you update your theme.</p>

				<p>Once you replace the theme menu with UberMenu, the residual styling should now be resolved, and UberMenu is manually integrated</p>

				</div>

				<a href="#" class="umd-tool-step-btn umd-tool-step-done">Done</a>
				<a href="#" class="umd-tool-step-btn umd-tool-step-prev">Back</a>


			</div>


		</div>
	</div>
</div>