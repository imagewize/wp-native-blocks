/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Call-to-Action section template with minimal configuration
 * Customize the TEMPLATE array below to match your needs
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Call to Action Heading',
  }],
  ['core/paragraph', {
    content: 'Add your call-to-action description here.',
  }],
  ['core/buttons', {}, [
    ['core/button', {
      text: 'Primary Action',
      url: '#',
    }]
  ]]
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
