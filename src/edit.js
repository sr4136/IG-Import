import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function Edit() {
	return (
		<section {...useBlockProps()}>
			<InnerBlocks />
		</section>
	);
}
