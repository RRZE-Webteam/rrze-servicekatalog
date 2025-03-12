import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ComboboxControl, TextControl, RadioControl, CheckboxControl, ToggleControl, BaseControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';

import './editor.scss';


export default ({ attributes, setAttributes }) => {

	const blockProps = useBlockProps();
	const {numServices } = attributes;
	const [layout, setLayout] = useState( attributes.layout || 'grid' );
	const [orderBy, setOrderBy] = useState( attributes.orderBy || 'commitment' );
	const [selectedTargetGroups, setSelectedTargetGroups] = useState(attributes.selectedTargetGroups || []);
	const [selectedCommitments, setSelectedCommitments] = useState(attributes.selectedCommitments || []);
	const [selectedTags, setSelectedTags] = useState(attributes.selectedTags || []);
	const [selectedServices, setSelectedServices] = useState(attributes.selectedServices || []);
	const {showSearchform} = attributes;
	const {showDisplaySwitcher} = attributes;
	const {showPdf} = attributes;
	const [selectedShowItems, setselectedShowItems] = useState(attributes.selectedShowItems || []);

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Hidden Items Options
	 * * * * * * * * * * * * * * * * * * * * * * * */

	const hiddenItemsOptions = [
		{ value: 'thumbnail', label: __('Thumbnail', 'rrze-servicekatalog') },
		{ value: 'commitment', label: __('Commitment', 'rrze-servicekatalog') },
		{ value: 'group', label: __('Target group', 'rrze-servicekatalog') },
		{ value: 'tag', label: __('Tag', 'rrze-servicekatalog') },
		{ value: 'url-portal', label: __('Portal link', 'rrze-servicekatalog') },
		{ value: 'url-description', label: __('Description link', 'rrze-servicekatalog') },
		{ value: 'url-tutorial', label: __('Tutorial link', 'rrze-servicekatalog') },
		{ value: 'url-video', label: __('Video link', 'rrze-servicekatalog') },
		{ value: 'urls', label: __('All links', 'rrze-servicekatalog') }
	];

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Target Group Options
	 * * * * * * * * * * * * * * * * * * * * * * * */

	// Begriffe der Taxonomie abrufen (z. B. Kategorien)
	const targetgroups = useSelect(select => {
		return select('core').getEntityRecords('taxonomy', 'rrze-service-target-group', { per_page: -1 }) || [];
	}, []);

	// Funktion zur Aktualisierung der Mehrfachauswahl
	const onAddTargetGroup = (targetgroupId) => {
		if (!selectedTargetGroups.includes(targetgroupId)) {
			const newTargetGroups = [...selectedTargetGroups, targetgroupId];
			setSelectedTargetGroups(newTargetGroups);
			setAttributes({ selectedTargetGroups: newTargetGroups });
		}
	};

	const onRemoveTargetGroup = (targetgroupId) => {
		const newTargetGroups = selectedTargetGroups.filter(id => id !== targetgroupId);
		setSelectedTargetGroups(newTargetGroups);
		setAttributes({ selectedTargetGroups: newTargetGroups });
	};

	// Begriffe für die Combobox-Optionen aufbereiten
	const targetgroupOptions = targetgroups ? targetgroups.map(targetgroup => ({
		label: targetgroup.name,
		value: targetgroup.slug
	})) : [];

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Commitment Options
	 * * * * * * * * * * * * * * * * * * * * * * * */

	const commitments = useSelect(select => {
		return select('core').getEntityRecords('taxonomy', 'rrze-service-commitment', { per_page: -1 }) || [];
	}, []);

	const onAddCommitment = (commitmentId) => {
		if (!selectedCommitments.includes(commitmentId)) {
			const newCommitments = [...selectedCommitments, commitmentId];
			setSelectedCommitments(newCommitments);
			setAttributes({ selectedCommitments: newCommitments });
		}
	};

	const onRemoveCommitment = (commitmentId) => {
		const newCommitments = selectedCommitments.filter(id => id !== commitmentId);
		setSelectedCommitments(newCommitments);
		setAttributes({ selectedCommitments: newCommitments });
	};

	const commitmentOptions = commitments ? commitments.map(commitment => ({
		label: commitment.name,
		value: commitment.slug
	})) : [];

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Tags Options
	 * * * * * * * * * * * * * * * * * * * * * * * */

	const tags = useSelect(select => {
		return select('core').getEntityRecords('taxonomy', 'rrze-service-tag', { per_page: -1 }) || [];
	}, []);

	const onAddTag = (tagId) => {
		if (!selectedTags.includes(tagId)) {
			const newTags = [...selectedTags, tagId];
			setSelectedTags(newTags);
			setAttributes({ selectedTags: newTags });
		}
	};

	const onRemoveTag = (tagId) => {
		const newTags = selectedTags.filter(id => id !== tagId);
		setSelectedTags(newTags);
		setAttributes({ selectedTags: newTags });
	};

	const tagOptions = tags ? tags.map(tag => ({
		label: tag.name,
		value: tag.slug
	})) : [];

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Services Options
	 * * * * * * * * * * * * * * * * * * * * * * * */

	// Posts abrufen
	const services = useSelect(select => {
		return select('core').getEntityRecords('postType', 'rrze-service') || [];
	}, []);

	// Begriffe für die Combobox-Optionen aufbereiten
	const serviceOptions = services ? services.map(service => ({
		label: service.title.rendered,
		value: service.id
	})) : [];

	// Funktion zur Aktualisierung der Mehrfachauswahl
	const onAddService = (serviceId) => {
		if (!selectedServices.includes(serviceId)) {
			const newServices = [...selectedServices, serviceId];
			setSelectedServices(newServices);
			setAttributes({ selectedServices: newServices });
		}
	};

	const onRemoveService = (serviceId) => {
		const newServices = selectedServices.filter(id => id !== serviceId);
		setSelectedServices(newServices);
		setAttributes({ selectedServices: newServices });
	};

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Functions
	 * * * * * * * * * * * * * * * * * * * * * * * */

	// Funktion zur Aktualisierung der numerischen Eingabe
	const onChangeNumber = (value) => {
		// Sicherstellen, dass nur Zahlen gespeichert werden
		const newNumber = parseInt(value, 10);
		if (!isNaN(newNumber) && newNumber >= -1) {
			setAttributes({ numServices: newNumber });
		} else {
			setAttributes({ numServices: '' });
		}
	};

	const onChangeLayout = (value) => {
		setLayout( value );
		setAttributes({layout: value});
	};

	const onChangeOrderBy = (value) => {
		setOrderBy( value );
		setAttributes({orderBy: value});
	};

	const toggleHiddenItems = (value) => {
		const newSelectedShowItems = selectedShowItems.includes(value)
			? selectedShowItems.filter((item) => item !== value) // Entfernen, falls bereits gewählt
			: [...selectedShowItems, value]; // Hinzufügen, falls nicht gewählt
		setselectedShowItems(newSelectedShowItems);
		setAttributes({ selectedShowItems: newSelectedShowItems });
	};

	/* * * * * * * * * * * * * * * * * * * * * * * *
	 * Controls
	 * * * * * * * * * * * * * * * * * * * * * * * */

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Layout', 'rrze-servicekatalog')}>
					<BaseControl label="Hinweis">
						<p>Hier kannst du die Einstellungen für den Block vornehmen.</p>
					</BaseControl>
					<RadioControl
						label={__('Layout', 'rrze-servicekatalog')}
						selected={ layout }
						options={ [
							{ label: __('Grid', 'rrze-servicekatalog'), value: 'grid' },
							{ label: __('List', 'rrze-servicekatalog'), value: 'list' },
						] }
						onChange={onChangeLayout}
					/>
					<RadioControl
						label={__('Order', 'rrze-servicekatalog')}
						selected={ orderBy }
						options={ [
							{ label: __('Commitment', 'rrze-servicekatalog'), value: 'commitment' },
							{ label: __('Target Group', 'rrze-servicekatalog'), value: 'group' },
							{ label: __('Tag', 'rrze-servicekatalog'), value: 'service-tag' },
						] }
						onChange={onChangeOrderBy}
					/>
				</PanelBody>
				<PanelBody title={__('Show/Hide Items', 'rrze-servicekatalog')}>
					<BaseControl label={__('Overview', 'rrze-servicekatalog')}>
						<p>{__('Visibility of overview elements', 'rrze-servicekatalog')}</p>
					</BaseControl>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show Search Form', 'rrze-servicekatalog')}
						checked={!!showSearchform }
						onChange={() =>
							setAttributes({
								showSearchform: !showSearchform,
							})
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show Display Switcher', 'rrze-servicekatalog')}
						checked={ !!showDisplaySwitcher }
						onChange={() =>
							setAttributes({
								showDisplaySwitcher: !showDisplaySwitcher,
							})
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Show PDF Download', 'rrze-servicekatalog')}
						checked={ !!showPdf }
						onChange={() =>
							setAttributes({
								showPdf: !showPdf,
							})
						}
					/>
					<BaseControl label={__('Single Service', 'rrze-servicekatalog')}>
						<p>{__('Visibility of service attributes', 'rrze-servicekatalog')}</p>
					</BaseControl>
					{hiddenItemsOptions.map((option) => (
						<ToggleControl
							key={option.value}
							label={option.label}
							checked={selectedShowItems.includes(option.value)}
							onChange={() => toggleHiddenItems(option.value)}
						/>
					))}
				</PanelBody>
				<PanelBody title={__('Select Services', 'rrze-servicekatalog')}>
					<ComboboxControl
						label={__('Target Groups', 'rrze-servicekatalog')}
						options={targetgroupOptions}
						onChange={onAddTargetGroup}
					/>
					<div style={{marginTop: '10px'}}>
						<strong>{__('Selected Target Groups', 'rrze-servicekatalog')}:</strong>
						<ul>
							{selectedTargetGroups.map(targetgroupSlug => {
								const targetgroup = targetgroups.find(t => t.slug === targetgroupSlug);
								return (
									<li key={targetgroupSlug}>
										{targetgroup?.name}
										<button onClick={() => onRemoveTargetGroup(targetgroupSlug)} style={{marginLeft: '5px'}}>
											{__('Remove', 'rrze-servicekatalog')}
										</button>
									</li>
								);
							})}
						</ul>
					</div>
					<hr/>
					<ComboboxControl
						label={__('Commitments', 'rrze-servicekatalog')}
						options={commitmentOptions}
						onChange={onAddCommitment}
					/>
					<div style={{marginTop: '10px'}}>
						<strong>{__('Selected Commitments', 'rrze-servicekatalog')}:</strong>
						<ul>
							{selectedCommitments.map(commitmentSlug => {
								const commitment = commitments.find(t => t.slug === commitmentSlug);
								return (
									<li key={commitmentSlug}>
										{commitment?.name}
										<button onClick={() => onRemoveCommitment(commitmentSlug)} style={{marginLeft: '5px'}}>
											{__('Remove', 'rrze-servicekatalog')}
										</button>
									</li>
								);
							})}
						</ul>
					</div>
					<hr/>
					<ComboboxControl
						label={__('Tags', 'rrze-servicekatalog')}
						options={tagOptions}
						onChange={onAddTag}
					/>
					<div style={{marginTop: '10px'}}>
						<strong>{__('Selected Tags', 'rrze-servicekatalog')}:</strong>
						<ul>
							{selectedTags.map(tagSlug => {
								const tag = tags.find(t => t.slug === tagSlug);
								return (
									<li key={tagSlug}>
										{tag?.name}
										<button onClick={() => onRemoveTag(tagSlug)} style={{marginLeft: '5px'}}>
											{__('Remove', 'rrze-servicekatalog')}
										</button>
									</li>
								);
							})}
						</ul>
					</div>
					<hr/>
					<ComboboxControl
						label={__('Services', 'rrze-servicekatalog')}
						options={serviceOptions}
						onChange={onAddService}
					/>
					<div style={{marginTop: '10px'}}>
						<strong>{__('Selected Services', 'rrze-servicekatalog')}:</strong>
						<ul>
							{selectedServices.map(serviceId => {
								const service = services.find(t => t.id === serviceId);
								return (
									<li key={serviceId}>
										{service?.title.rendered}
										<button onClick={() => onRemoveService(serviceId)} style={{marginLeft: '5px'}}>
											{__('Remove', 'rrze-servicekatalog')}
										</button>
									</li>
								);
							})}
						</ul>
					</div>
					<hr/>
					<TextControl
						label={__('Count', 'rrze-servicekatalog')}
						type="number"
						value={numServices}
						onChange={onChangeNumber}
						help={__('How many services do you want to show? Enter -1 for all services.', 'rrze-servicekatalog')}
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="rrze/servicekatalog"
				attributes={attributes}
			/>
		</div>
	);
};
