/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Statistics section template with minimal configuration
 * Customize the TEMPLATE array below to match your needs
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Statistics Section',
  }],
  ['core/columns', {}, [
    ['core/column', {}, [
      ['core/heading', {
        level: 3,
        content: '100+',
      }],
      ['core/paragraph', {
        content: 'Statistic Label',
      }]
    ]],
    ['core/column', {}, [
      ['core/heading', {
        level: 3,
        content: '50+',
      }],
      ['core/paragraph', {
        content: 'Statistic Label',
      }]
    ]]
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
