/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * InnerBlocks template with minimal pre-configuration
 * Customize the TEMPLATE array below to match your needs
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Section Heading',
  }],
  ['core/paragraph', {
    content: 'Add your content here. This template provides structure without opinionated styling.',
  }]
];

/**
 * Edit function that renders in the admin
 */
export default function Edit() {
  const blockProps = useBlockProps({
    className: '{{BLOCK_CLASS_NAME}}'
  });

  return (
    <div { ...blockProps }>
      <InnerBlocks
        template={TEMPLATE}
        templateLock={false}
      />
    </div>
  );
}
