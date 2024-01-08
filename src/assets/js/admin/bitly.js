const TrbBitlyForm = ({
  setState,
  showNotices,
  spinnerActive,
  linkField,
  linkValidate,
  linkValidateClass,
  linkList,
}) => {

  const { Notice, TextControl, DropdownMenu, Button, Spinner } = wp.components;
  const { apiFetch } = wp;
  const el = wp.element.createElement;

  /**
   * Cria um novo alerta
   * @param {string} message
   * @param {string} status
   * @param {object} options
   */
  const createNotice = async (message, status, options) => {
    if ( ! message) return false;
    await setState({
      showNotices: el(Notice, {status: status || null, isDismissible: true, onRemove: () => setState({showNotices: null}), ...options}, message)
    });
  };

  /**
   * Executa a ação principal
   */
  const onSubmit = async (source) => {
    await setState({
      spinnerActive: true
    });

    try {

      const response = await apiFetch({
        path: '/tribuna/v1/shorter',
        method: 'POST',
        data: {
          link: linkField,
          source: source
        }
      });

      await updateLinkListElements(response);

      await setState({
        spinnerActive: false
      });

    } catch (error) {
      createNotice("Houston, estamos com problema! Não foi possível enviar a mensagem.", 'error');
      console.error(error);
    }
  };

  const updateLinkListElements = async (link) => {
    console.log(link);

    const element = el('li', { className: `linkitem linkitem--${link.sourceName}` },
      el('div', { className: 'linkitem__title' }, link.sourceTitle),
      el('input', {type: 'text', value: link.short, readonly: true, className: 'linkitem__link linkitem__link--short components-text-control__input'}),
      el('input', {type: 'text', value: link.long, readonly: true, className: 'linkitem__link linkitem__link--long components-text-control__input'}),
    );

    await setState({
      linkList: [ element, ...linkList ]
    });
  }

  /**
   * Seta os states dos campos
   */
  const onChangeLink = async (value) => {
    await setState({
      linkField: value
    });
    await setState({
      linkValidateClass: ( ! linkField) ? null : (linkValidate() ? "validate__success" : "validate__error")
    });
  }

  const onReset = async () => setState({
    showNotices: null,
    spinnerActive: false,
    linkField: "",
    linkValidateClass: null,
    linkList: [],
  });

  return el('div', null,
    showNotices,
    el('div', { className: 'actions-control' },
      el(TextControl, { label: "Link", value: linkField, disabled: spinnerActive, className: 'url-field '+linkValidateClass, onChange: onChangeLink }),
      ( ! linkValidate() ? null : (spinnerActive ? el(Spinner) : el(DropdownMenu, { icon: 'external', label: 'Encurtar', controls: [
        {
          title: 'Facebook',
          icon: 'facebook',
          onClick: () => onSubmit({ title: 'Facebook', name: 'facebook' })
        },
        {
          title: 'Twitter',
          icon: 'twitter',
          onClick: () => onSubmit({ title: 'Twitter', name: 'twitter' })
        },
        {
          title: 'Youtube',
          icon: 'video-alt3',
          onClick: () => onSubmit({ title: 'Youtube', name: 'youtube' })
        },
        {
          title: 'WhatsApp',
          icon: 'whatsapp',
          onClick: () => onSubmit({ title: 'WhatsApp', name: 'whatsapp' })
        },
        {
          title: 'Telegram',
          icon: 'format-chat',
          onClick: () => onSubmit({ title: 'Telegram', name: 'telegram' })
        },
        {
          title: 'Instagram',
          icon: 'instagram',
          onClick: () => onSubmit({ title: 'Instagram', name: 'instagram' })
        },
        {
          title: 'Newsletter',
          icon: 'email',
          onClick: () => onSubmit({ title: 'Newsletter', name: 'newsletter' })
        },
        {
          title: 'Messenger',
          icon: 'format-status',
          onClick: () => onSubmit({ title: 'Messenger', name: 'messenger' })
        }
      ] }))),
    ),
    (( ! linkList.length) ? null : el('div', { className: 'links-action' },
      el(Button, { isLink: true, onClick: onReset }, 'Limpar')
    )),
    el('ul', { className: 'links-control'}, linkList)
  );
};

const TrbBitlyComponent = wp.compose.compose(
  wp.compose.withState({
    showNotices: null,
    spinnerActive: false,
    linkField: "",
    linkValidateClass: null,
    linkList: [],
  }),
  wp.data.withDispatch((dispatch, ownProps) => {
    const { linkField } = ownProps;
    return {
      linkValidate: () => (linkField.match(/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/)) ? true : false
    };
  })
)(TrbBitlyForm);

ReactDOM.render(wp.element.createElement(TrbBitlyComponent), document.getElementById('TribunaBitlyWidget'));
