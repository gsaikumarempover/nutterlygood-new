// Block namespace + block name
const blockName = 'qode-quick-view-for-woocommerce/quick-view-button';

// Import block modules
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';

// Register block type function
registerBlockType(
	blockName,
	{
		icon: {
			src: <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
		},
		title: __( 'Qode Quick View Button', 'qode-quick-view-for-woocommerce' ),
		description: __( 'Block that displays quick view button', 'qode-quick-view-for-woocommerce' ),
		category: 'qode-woocommerce-blocks',
		keywords: [
			__( 'product', 'qode-quick-view-for-woocommerce' ),
			__( 'woocommerce', 'qode-quick-view-for-woocommerce' ),
			__( 'quick-view', 'qode-quick-view-for-woocommercee' ),
			__( 'qode', 'qode-quick-view-for-woocommerce' ),
		],
		edit,
		attributes: {
			item_id: {
				type: 'number',
				default: '',
			},
			button_type: {
				type: 'string',
				default: '',
			},
		}
	}
);
