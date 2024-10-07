const { registerBlockType } = wp.blocks;
const { serverSideRender: ServerSideRender } = wp;

const {
	useBlockProps,
	InspectorControls,
} = wp.blockEditor;

const {
	Panel,
	PanelBody,
	SelectControl,
	RangeControl,
} = wp.components;

const { Fragment } = wp.element;

registerBlockType( 'owc/mijn-taken', {
	apiVersion: 2,
	title: 'Mijn Taken',
	category: 'common',
	attributes: {
		zaakClient: { type: 'string', default: 'openzaak' },
		view: { type: 'string', default: 'default' },
		numberOfItems: { type: 'number', default: 2 },
	},
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const { zaakClient } = attributes;

		return (
			<div { ...blockProps }>
				<InspectorControls>
					<Panel>
						<PanelBody title="Zaaksysteem" initialOpen={ false }>
							<p>
								Selecteer het zaaksysteem waaruit de taken
								opgehaald moeten worden.
							</p>
							<SelectControl
								label="Zaaksysteem"
								value={ zaakClient }
								options={ [
									{ label: 'OpenZaak', value: 'openzaak' },
									{
										label: 'Decos JOIN',
										value: 'decos-join',
									},
									{
										label: 'Rx.Mission',
										value: 'rx-mission',
									},
									{ label: 'xxllnc', value: 'xxllnc' },
									{ label: 'Procura', value: 'procura' },
								] }
								onChange={ ( newzaakClient ) =>
									setAttributes( {
										zaakClient: newzaakClient,
									} )
								}
							/>
						</PanelBody>
						{ attributes.view === 'current' && (
							<PanelBody>
								<RangeControl
									min={ 1 }
									max={ 20 }
									label="Aantal"
									help="Het aantal taken dat getoond moeten worden."
									value={ attributes.numberOfItems }
									onChange={ ( value ) =>
										setAttributes( {
											numberOfItems: value,
										} )
									}
								/>
							</PanelBody>
						) }
						<PanelBody title="Weergave" initialOpen={ false }>
							<SelectControl
								label="Selecteer de weergave van de taken"
								value={ attributes.view }
								options={ [
									{
										label: 'Standaard',
										value: 'default',
									},
									{
										label: 'Lopende Zaken',
										value: 'current',
									},
								] }
								onChange={ ( newView ) =>
									setAttributes( { view: newView } )
								}
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>

				{ attributes.view === 'default' ? (
					<p>Standaardweergave</p>
				) : (
					<>
						<ul
							className="zaak-tabs | nav nav-tabs"
							id="zaak-tabs"
							role="tablist"
						>
							<li className="nav-item" role="presentation">
								<button className="zaak-tabs-link | nav-link active">
									Lopende taken
								</button>
							</li>
							<li className="nav-item" role="presentation">
								<button className="zaak-tabs-link | nav-link">
									Afgeronde taken
								</button>
							</li>
						</ul>

						<div className="tab-content" id="myTabContent">
							<div className="tab-pane fade show active">
								<div className="zaak-card-wrapper">
									<div className="zaak-card">
										<svg
											className="zaak-card-svg"
											width="385"
											height="200"
											viewBox="0 0 385 200"
											fill="#F1F1F1"
											xmlns="http://www.w3.org/2000/svg"
											preserveAspectRatio="none"
										>
											<path d="M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z" />
										</svg>
										<h2 className="zaak-card-title">
											Aanvragen uittreksel BRP
										</h2>
										<div className="zaak-card-footer">
											<div className="zaak-card-date">
												12 december 2023
											</div>
											<div className="zaak-card-tag">
												Dummy content
											</div>
											<svg
												className="zaak-card-arrow"
												width="24"
												height="24"
												viewBox="0 0 24 24"
												xmlns="http://www.w3.org/2000/svg"
											>
												<path d="M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z" />
											</svg>
										</div>
									</div>

									<div className="zaak-card">
										<svg
											className="zaak-card-svg"
											width="385"
											height="200"
											viewBox="0 0 385 200"
											fill="#F1F1F1"
											xmlns="http://www.w3.org/2000/svg"
											preserveAspectRatio="none"
										>
											<path d="M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z" />
										</svg>
										<h2 className="zaak-card-title">
											Aanmelden straatfeest
										</h2>
										<div className="zaak-card-footer">
											<div className="zaak-card-date">
												15 oktober 2023
											</div>
											<svg
												className="zaak-card-arrow"
												width="24"
												height="24"
												viewBox="0 0 24 24"
												xmlns="http://www.w3.org/2000/svg"
											>
												<path d="M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z" />
											</svg>
										</div>
									</div>
									<div className="zaak-card">
										<svg
											className="zaak-card-svg"
											width="385"
											height="200"
											viewBox="0 0 385 200"
											fill="#F1F1F1"
											xmlns="http://www.w3.org/2000/svg"
											preserveAspectRatio="none"
										>
											<path d="M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z" />
										</svg>
										<h2 className="zaak-card-title">
											Aanvraag rijbewijs
										</h2>
										<div className="zaak-card-footer">
											<div className="zaak-card-date">
												20 januari 2023
											</div>
											<svg
												className="zaak-card-arrow"
												width="24"
												height="24"
												viewBox="0 0 24 24"
												xmlns="http://www.w3.org/2000/svg"
											>
												<path d="M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z" />
											</svg>
										</div>
									</div>

									<div className="zaak-card">
										<svg
											className="zaak-card-svg"
											width="385"
											height="200"
											viewBox="0 0 385 200"
											fill="#F1F1F1"
											xmlns="http://www.w3.org/2000/svg"
											preserveAspectRatio="none"
										>
											<path d="M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z" />
										</svg>
										<h2 className="zaak-card-title">
											Aanvragen leefbaarheidsbudget
										</h2>
										<div className="zaak-card-footer">
											<div className="zaak-card-date">
												11 januari 2023
											</div>
											<svg
												className="zaak-card-arrow"
												width="24"
												height="24"
												viewBox="0 0 24 24"
												xmlns="http://www.w3.org/2000/svg"
											>
												<path d="M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z" />
											</svg>
										</div>
									</div>
								</div>
							</div>
						</div>
					</>
				) }
			</div>
		);
	},
	save: ( { className } ) => {
		return <section className={ className }></section>;
	},
} );
