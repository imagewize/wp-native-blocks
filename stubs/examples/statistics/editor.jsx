/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Statistics section template with multi-column layout
 * Edit this template to customize the default structure
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Our Impact',
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
        margin: { bottom: '3rem' }
      }
    }
  }],
  ['core/columns', {
    className: 'statistics-row',
    style: {
      spacing: {
        blockGap: '3.75rem'
      }
    }
  }, [
    ['core/column', {}, [
      ['core/paragraph', {
        content: 'STATISTIC 1',
        fontFamily: 'montserrat',
        fontSize: 'sm',
        textAlign: 'center',
        textColor: 'main',
        style: {
          typography: {
            fontWeight: '700',
            letterSpacing: '0.1em'
          },
          spacing: {
            margin: { bottom: '0.5rem' }
          }
        }
      }],
      ['core/heading', {
        level: 3,
        content: '500+',
        fontFamily: 'montserrat',
        fontSize: '4xl',
        textAlign: 'center',
        textColor: 'main',
        style: {
          typography: {
            fontWeight: '700',
            lineHeight: '1'
          },
          spacing: {
            margin: { bottom: '0.5rem' }
          }
        }
      }],
      ['core/paragraph', {
        content: 'Happy Clients',
        fontFamily: 'open-sans',
        fontSize: 'base',
        textAlign: 'center',
        textColor: 'secondary',
        style: {
          typography: {
            lineHeight: '1.5'
          }
        }
      }]
    ]],
    ['core/column', {}, [
      ['core/paragraph', {
        content: 'STATISTIC 2',
        fontFamily: 'montserrat',
        fontSize: 'sm',
        textAlign: 'center',
        textColor: 'main',
        style: {
          typography: {
            fontWeight: '700',
            letterSpacing: '0.1em'
          },
          spacing: {
            margin: { bottom: '0.5rem' }
          }
        }
      }],
      ['core/heading', {
        level: 3,
        content: '10+',
        fontFamily: 'montserrat',
        fontSize: '4xl',
        textAlign: 'center',
        textColor: 'main',
        style: {
          typography: {
            fontWeight: '700',
            lineHeight: '1'
          },
          spacing: {
            margin: { bottom: '0.5rem' }
          }
        }
      }],
      ['core/paragraph', {
        content: 'Years Experience',
        fontFamily: 'open-sans',
        fontSize: 'base',
        textAlign: 'center',
        textColor: 'secondary',
        style: {
          typography: {
            lineHeight: '1.5'
          }
        }
      }]
    ]]
  ]],
  ['core/heading', {
    level: 2,
    content: 'Why Choose Us',
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
        margin: { top: '4rem', bottom: '1rem' }
      }
    }
  }],
  ['core/paragraph', {
    content: 'We deliver exceptional results that exceed expectations.',
    fontFamily: 'open-sans',
    fontSize: 'lg',
    textAlign: 'center',
    textColor: 'secondary',
    style: {
      typography: {
        lineHeight: '1.7'
      },
      spacing: {
        margin: { bottom: '3rem' }
      }
    }
  }],
  ['core/columns', {
    className: 'benefits-row',
    style: {
      spacing: {
        blockGap: '1.875rem'
      }
    }
  }, [
    ['core/column', {}, [
      ['core/group', {
        className: 'benefit-item',
        style: {
          spacing: {
            padding: { top: '2rem', right: '1.5rem', bottom: '2rem', left: '1.5rem' }
          }
        }
      }, [
        ['core/heading', {
          level: 4,
          content: 'Benefit 1',
          fontFamily: 'montserrat',
          fontSize: 'xl',
          textAlign: 'center',
          textColor: 'main',
          style: {
            typography: {
              fontWeight: '600',
              lineHeight: '1.3'
            },
            spacing: {
              margin: { bottom: '1rem' }
            }
          }
        }],
        ['core/paragraph', {
          content: 'Description of the first key benefit.',
          fontFamily: 'open-sans',
          fontSize: 'base',
          textAlign: 'center',
          textColor: 'secondary',
          style: {
            typography: {
              lineHeight: '1.7'
            }
          }
        }]
      ]]
    ]],
    ['core/column', {}, [
      ['core/group', {
        className: 'benefit-item',
        style: {
          spacing: {
            padding: { top: '2rem', right: '1.5rem', bottom: '2rem', left: '1.5rem' }
          }
        }
      }, [
        ['core/heading', {
          level: 4,
          content: 'Benefit 2',
          fontFamily: 'montserrat',
          fontSize: 'xl',
          textAlign: 'center',
          textColor: 'main',
          style: {
            typography: {
              fontWeight: '600',
              lineHeight: '1.3'
            },
            spacing: {
              margin: { bottom: '1rem' }
            }
          }
        }],
        ['core/paragraph', {
          content: 'Description of the second key benefit.',
          fontFamily: 'open-sans',
          fontSize: 'base',
          textAlign: 'center',
          textColor: 'secondary',
          style: {
            typography: {
              lineHeight: '1.7'
            }
          }
        }]
      ]]
    ]],
    ['core/column', {}, [
      ['core/group', {
        className: 'benefit-item',
        style: {
          spacing: {
            padding: { top: '2rem', right: '1.5rem', bottom: '2rem', left: '1.5rem' }
          }
        }
      }, [
        ['core/heading', {
          level: 4,
          content: 'Benefit 3',
          fontFamily: 'montserrat',
          fontSize: 'xl',
          textAlign: 'center',
          textColor: 'main',
          style: {
            typography: {
              fontWeight: '600',
              lineHeight: '1.3'
            },
            spacing: {
              margin: { bottom: '1rem' }
            }
          }
        }],
        ['core/paragraph', {
          content: 'Description of the third key benefit.',
          fontFamily: 'open-sans',
          fontSize: 'base',
          textAlign: 'center',
          textColor: 'secondary',
          style: {
            typography: {
              lineHeight: '1.7'
            }
          }
        }]
      ]]
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
