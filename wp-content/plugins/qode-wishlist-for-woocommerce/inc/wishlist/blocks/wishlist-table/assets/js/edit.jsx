// Import block modules
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';

class WishlistTableBlockEdit extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			elementLoading: false,
			blockHTMLContent: '',
		};
	}

	componentDidMount() {
		this.setState( {
			elementLoading: true,
		} );

		apiFetch(
			{
				method: 'POST',
				path: '/qode-wishlist-for-woocommerce/v1/render-wishlist-table',
			}
		).then(
			response =>
			{
				this.setState( {
					elementLoading: false,
				} );

				if ( 'success' === response.status ) {
					this.setState( {
						blockHTMLContent: response.data,
					} );
				}
			}
		);
	}

	render() {
		const stateInstance  = { ...this.state };
		let blockHTMLContent = stateInstance.blockHTMLContent;

		return (
			<>
				{
					stateInstance.elementLoading
					?
					<div className="qode-woocommerce-block">
						<Spinner />
					</div>
					:
					<div
						className="qode-woocommerce-block qwfw--wishlist-table"
						dangerouslySetInnerHTML={{
							__html: stateInstance.blockHTMLContent ? blockHTMLContent : __('Wishlist Table Block', 'qode-wishlist-for-woocommerce')
						}}
					/>
				}
			</>
		)
	}
}

export default WishlistTableBlockEdit;
