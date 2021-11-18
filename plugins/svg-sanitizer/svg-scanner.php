<?php

function svg_scanner($path)
{
	if(! $path)
	{
		return 'No files to scan specified';
	}
	require_once( __DIR__ . '/data/AttributeInterface.php' );
	require_once( __DIR__ . '/data/TagInterface.php' );
	require_once( __DIR__ . '/data/AllowedAttributes.php' );
	require_once( __DIR__ . '/data/AllowedTags.php' );
	require_once( __DIR__ . '/data/XPath.php' );
	require_once( __DIR__ . '/ElementReference/Resolver.php' );
	require_once( __DIR__ . '/ElementReference/Subject.php' );
	require_once( __DIR__ . '/ElementReference/Usage.php' );
	require_once( __DIR__ . '/Exceptions/NestingException.php' );
	require_once( __DIR__ . '/Helper.php' );
	require_once( __DIR__ . '/Sanitizer.php' );

	/*
	 * Set up results array, to
	 * be printed on exit.
	 */
	$results = array(
		'totals' => array(
			'errors' => 0,
		),

		'files' => array(
		),
	);

	/*
	 * Initialize the SVG scanner.
	 *
	 * Make sure to allow custom attributes,
	 * and to remove remote references.
	 */
	$sanitizer = new enshrined\svgSanitize\Sanitizer();

	$sanitizer->removeRemoteReferences( true );

	/*
	 * Read SVG file.
	 */
	$svg_file = @file_get_contents(ABSOLUTE_PATH.$path);

	/*
	 * If not found, report that and continue.
	 */
	if ( false === $svg_file )
	{
		return 'File specified could not be read ('.$path.')';
	}

	/*
	 * Sanitize file and get issues found.
	 */
	$sanitize_status = $sanitizer->sanitize( $svg_file );

	$xml_issues = $sanitizer->getXmlIssues();

	/*
	 * If we find no issues, simply note that.
	 */
	if ( empty( $xml_issues ) && ( false !== $sanitize_status ) ) {
		return false;
	}

	/*
	 * Could not sanitize the file.
	 */
	else if (
		( '' === $sanitize_status ) ||
		( false === $sanitize_status )
	) {
		return 'Unable to sanitize file \'' . $path . '\'';
	}

	/*
	 * If we find issues, note it and update statistics.
	 */

	else {
		return implode(' ', $xml_issues);
	}
}