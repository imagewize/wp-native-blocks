/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Call-to-Action section template with heading, description, and buttons
 * Edit this template to customize the default structure
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Ready to Get Started?',
    fontFamily: 'montserrat',
    fontSize: '3xl',
    textAlign: 'center',
    textColor: 'main',
    style: {
      typography: {
        fontWeight: '700',
        lineHeight: '1.2'
      },
      spacing: {
        margin: { bottom: '1.5rem' }
      }
    }
  }],
  ['core/paragraph', {
    content: 'Join thousands of satisfied customers who trust us with their business.',
    fontFamily: 'open-sans',
    fontSize: 'lg',
    textAlign: 'center',
    textColor: 'secondary',
    style: {
      typography: {
        lineHeight: '1.7'
      },
      spacing: {
        margin: { bottom: '2.5rem' }
      }
    }
  }],
  ['core/buttons', {
    layout: {
      type: 'flex',
      justifyContent: 'center'
    },
    style: {
      spacing: {
        blockGap: '1rem'
      }
    }
  }, [
    ['core/button', {
      text: 'Get Started',
      url: '#',
      style: {
        border: {
          radius: '0.375rem'
        },
        spacing: {
          padding: {
            top: '1rem',
            right: '2rem',
            bottom: '1rem',
            left: '2rem'
          }
        }
      },
      backgroundColor: 'main',
      textColor: 'base'
    }],
    ['core/button', {
      text: 'Learn More',
      url: '#',
      className: 'is-style-outline',
      style: {
        border: {
          radius: '0.375rem',
          width: '2px'
        },
        spacing: {
          padding: {
            top: '1rem',
            right: '2rem',
            bottom: '1rem',
            left: '2rem'
          }
        }
      },
      borderColor: 'main',
      textColor: 'main'
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
