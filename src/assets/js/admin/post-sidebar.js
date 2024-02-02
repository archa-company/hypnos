const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel, PluginSidebar, PluginSidebarMoreMenuItem } =
  wp.editPost;
const {
  PanelBody,
  TextControl,
  SelectControl,
  Popover,
  DateTimePicker,
  ToggleControl,
  Button,
  Spinner,
  DropdownMenu,
} = wp.components;
const { compose, withState } = wp.compose;
const { withDispatch, withSelect } = wp.data;
const { apiFetch } = wp;
const wpData = wp.data.select("core/editor");
const el = wp.element.createElement;

/**
 * Cria um novo alerta
 * @param {string} message
 * @param {string} status
 * @param {object} options
 */
const createNotice = (message, status, options) => {
  if (!message) return false;
  wp.data.dispatch("core/notices").createNotice(
    status ?? null,
    message, // Text string to display.
    {
      isDismissible: true, // Whether the user can dismiss the notice.
      ...options,
      // Any actions the user can perform.
      // actions: [
      //   {
      //     url: '#',
      //     label: 'View post',
      //   },
      // ],
    }
  );
};

/**
 *
 * PAINEL DE PUSH NOTIFICATION
 *
 */
const MorpheusMessageForm = ({
  titleField,
  titleValidate,
  titleValidateClass,
  messageField,
  messageValidate,
  messageValidateClass,
  isSchedule,
  schedulePickerVisible,
  scheduleField,
  submitDisabledState,
  submitLabel,
  spinnerActive,
  setState,
}) => {
  /**
   * Reseta o estado do botão submit
   */
  const resetSubmit = async () => {
    await setState({
      submitDisabledState: false,
      submitLabel: isSchedule && scheduleField ? "Agendar" : "Enviar",
      spinnerActive: false,
    });
  };

  const sendMessage = async () => {
    const sendData = {
      // provider: 'Notix',
      title: titleField,
      message: messageField,
      schedule:
        isSchedule && scheduleField ? new Date(scheduleField).toJSON() : null,
      url: wp.data.select("core/editor").getPermalink(),
      postId: wp.data.select("core/editor").getCurrentPostId(),
      thumbnailId: wp.data
        .select("core/editor")
        .getEditedPostAttribute("featured_media"),
      blogPath: window.location.pathname.replace(/wp-admin\/.*/, ""),
      // data          : '',
      // large_icon    : '',
    };

    await sendToApiMessage(sendData);
  };

  const sendToApiMessage = async (message) => {
    try {
      const response = await apiFetch({
        method: "POST",
        url: `${window.location.origin}/wp-json/morpheus/v1/push-message`,
        data: message,
      });
      console.log(response);
      /**
       * Mensagem de sucesso após o envio
       */
      createNotice(
        response.response.notice.text,
        response.response.notice.status || "success"
      );
    } catch (error) {
      console.error(error);
      createNotice(
        "Houston, estamos com problema! Não foi possível enviar a mensagem.",
        "error"
      );
    }
  };

  /**
   * Executa a ação principal
   */
  const onSubmit = async () => {
    await setState({
      submitDisabledState: true,
      submitLabel: isSchedule && scheduleField ? "Agendando..." : "Enviando...",
      spinnerActive: true,
    });

    /**
     * Faz a validação antes de enviar
     */
    if (!(await validate())) return false;

    /**
     * Envia a mensagem
     * @todo remover setTimeout
     */
    await sendMessage();

    /**
     * Reseta o formulário
     */
    resetSubmit();
  };

  /**
   * Validações pré envio
   */
  const validate = async () => {
    /**
     * @todo validar se tem thumbnail no post
     */
    // if ( ! wp.data.select('core/editor').getEditedPostAttribute('featured_media')) {
    //   await resetSubmit();
    //   return false;
    // }

    if (
      wp.data.select("core/editor").getEditedPostAttribute("status") !=
      "publish"
    ) {
      createNotice(
        "Este post não está publicado. Você precisa publica-lo antes de enviar a notificação",
        "error"
      );
      await resetSubmit();
      return false;
    }

    if (!titleField) {
      createNotice("É obrigatório informar um título para o envio.", "warning");
      await resetSubmit();
      return false;
    }
    if (!messageField) {
      createNotice(
        "É obrigatório informar uma mensagem para o envio.",
        "warning"
      );
      await resetSubmit();
      return false;
    }

    return true;
  };

  /**
   * Seta os states dos campos
   */
  const onChangeTitle = async (value) => {
    await setState({
      titleField: value,
    });
    await updateTitleClass();
  };
  const onChangeMessage = async (value) => {
    await setState({
      messageField: value,
    });
    await setState({
      messageValidateClass: messageValidate(),
    });
  };
  const onChangeIsSchedule = async () => {
    await setState({
      isSchedule: !isSchedule,
      submitLabel: !isSchedule ? "Agendar" : "Enviar",
    });
  };
  const onChangeSchedulePickerVisible = async () => {
    await setState({
      schedulePickerVisible: !schedulePickerVisible,
    });
  };
  const onChangeSchedule = async (value) => {
    await setState({
      scheduleField: value,
    });
  };
  const copyPostTitle = async () => {
    await setState({
      titleField: wp.data.select("core/editor").getEditedPostAttribute("title"),
    });
    await updateTitleClass();
  };

  const updateTitleClass = async () => {
    await setState({
      titleValidateClass: titleValidate(),
    });
  };

  return el(
    PanelBody,
    { title: "Push Notification", icon: "megaphone", intialOpen: true },
    [
      el(TextControl, {
        label: "Título",
        value: titleField,
        disabled: submitDisabledState,
        className: titleValidateClass,
        help: `${titleField.length} caracteres`,
        onChange: onChangeTitle,
      }),
      el(TextControl, {
        label: "Mensagem",
        value: messageField,
        disabled: submitDisabledState,
        className: messageValidateClass,
        help: `${messageField.length} caracteres`,
        onChange: onChangeMessage,
      }),
      el("div", { className: "schedule-control" }, [
        el(ToggleControl, {
          label: "Agendar o envio?",
          help: "Envia a mensagem somente na data e hora informada",
          checked: isSchedule,
          onChange: onChangeIsSchedule,
        }),
        !isSchedule
          ? null
          : el("div", { className: "schedule-text-control" }, [
              el("span", { className: "schedule-text" }, "Enviar em"),
              el(
                Button,
                { isLink: true, onClick: onChangeSchedulePickerVisible },
                [
                  scheduleField
                    ? new Date(scheduleField).toLocaleString()
                    : "Informe a data e hora",
                  !schedulePickerVisible
                    ? null
                    : el(Popover, { position: "middle left" }, [
                        el(DateTimePicker, {
                          currentDate: scheduleField,
                          onChange: onChangeSchedule,
                          className: "schedule-datepicker",
                        }),
                      ]),
                ]
              ),
            ]),
      ]),
      el("div", { className: "actions-control" }, [
        el(
          Button,
          { isPrimary: true, disabled: submitDisabledState, onClick: onSubmit },
          submitLabel
        ),
        spinnerActive ? el(Spinner) : null,
        el(
          Button,
          {
            isLink: true,
            disabled: submitDisabledState,
            onClick: copyPostTitle,
          },
          "Copiar título"
        ),
      ]),
    ]
  );
};
const MorpheusMessageComponent = compose(
  withState({
    titleField: "",
    titleValidateClass: "validate__error",
    messageField: "",
    messageValidateClass: "validate__error",
    isSchedule: false,
    schedulePickerVisible: false,
    scheduleField: null,
    submitLabel: "Enviar",
    submitDisabledState: false,
    spinnerActive: false,
  }),
  withDispatch((dispatch, ownProps) => {
    const { titleField, messageField } = ownProps;
    return {
      titleValidate: () =>
        !titleField.length
          ? "validate__error"
          : titleField.length <= 47
          ? "validate__success"
          : "validate__warning",
      messageValidate: () =>
        !messageField.length
          ? "validate__error"
          : messageField.length <= 47
          ? "validate__success"
          : "validate__warning",
    };
  })
)(MorpheusMessageForm);

/**
 *
 * REGISTRO DO SIDEBAR
 *
 */
registerPlugin("morpheus-sidebar", {
  icon: "games",
  render: () => [
    el(PluginSidebarMoreMenuItem, { target: "morpheus-sidebar" }, "Morpheus"),
    el(
      PluginSidebar,
      {
        name: "morpheus-sidebar",
        title: "Morpheus",
        className: "morpheus-sidebar",
      },
      [el(MorpheusMessageComponent)]
    ),
  ],
});
