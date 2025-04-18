/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { createElement } from '@wordpress/element';

/**
 * Internal dependencies
 */
import metadata from './block.json';

/**
 * Register the block
 */
registerBlockType(metadata.name, {
  ...metadata,
  
  /**
   * Edit function that renders in the admin
   */
  edit: function Edit() {
    const blockProps = useBlockProps();
    
    return (
      <div { ...blockProps }>
        <p>{ __('Example block content. Edit this template to create your own block.', 'imagewize') }</p>
        <InnerBlocks />
      </div>
    );
  },
  
  /**
   * Save function that defines output on the frontend
   */
  save: function Save() {
    const blockProps = useBlockProps.save();
    
    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
});