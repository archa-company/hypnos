const MorpheusMessageForm = ({
  showNotices,
  titleField,
  titleValidate,
  titleValidateClass,
  messageField,
  messageValidate,
  messageValidateClass,
  linkField,
  linkValidate,
  linkValidateClass,
  isSchedule,
  schedulePickerVisible,
  scheduleField,
  submitDisabledState,
  submitLabel,
  spinnerActive,
  setState,
}) => {
  const {
    Notice,
    TextControl,
    Popover,
    DateTimePicker,
    ToggleControl,
    Button,
    Spinner,
  } = wp.components;
  const { apiFetch } = wp;
  const el = wp.element.createElement;

  /**
   * Cria um novo alerta
   * @param {string} message
   * @param {string} status
   * @param {object} options
   */
  const createNotice = async (message, status, options) => {
    if (!message) return false;
    await setState({
      showNotices: el(
        Notice,
        {
          status: status || null,
          isDismissible: true,
          onRemove: onNoticeRemove,
          ...options,
        },
        message
      ),
    });
  };

  /**
   * Remove o alerta
   */
  const onNoticeRemove = async () => {
    await setState({
      showNotices: null,
    });
  };

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
      title: titleField,
      message: messageField,
      schedule:
        isSchedule && scheduleField ? new Date(scheduleField).toJSON() : null,
      url: linkField,
      postId: null,
      thumbnailId: null,
      // data          : '',
      // large_icon    : '',
    };
    try {
      const response = await apiFetch({
        path: "/morpheus/v1/push-message",
        method: "POST",
        data: sendData,
      });

      return response;
    } catch (error) {
      createNotice(
        "Houston, estamos com problema! Não foi possível enviar a mensagem.",
        "error"
      );
      console.error(error);
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
    const response = await sendMessage();
    console.log(response);

    /**
     * Reseta o formulário
     */
    resetSubmit();

    /**
     * Mensagem de sucesso após o envio
     */
    createNotice(
      response.response.notice.text,
      response.response.notice.status || "success"
    );
  };

  /**
   * Validações pré envio
   */
  const validate = async () => {
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
  const onChangeLink = async (value) => {
    await setState({
      linkField: value,
    });
    await setState({
      linkValidateClass: linkValidate(),
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

  const updateTitleClass = async () => {
    await setState({
      titleValidateClass: titleValidate(),
    });
  };

  return el(
    "div",
    null,
    showNotices || null,
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
    el(TextControl, {
      label: "Link",
      value: linkField,
      disabled: submitDisabledState,
      className: linkValidateClass,
      onChange: onChangeLink,
    }),
    el(
      "div",
      { className: "schedule-control" },
      el(ToggleControl, {
        label: "Agendar o envio?",
        help: "Envia a mensagem somente na data e hora informada",
        checked: isSchedule,
        onChange: onChangeIsSchedule,
      }),
      !isSchedule
        ? null
        : el(
            "div",
            { className: "schedule-text-control" },
            el("span", { className: "schedule-text" }, "Enviar em"),
            el(
              Button,
              { isLink: true, onClick: onChangeSchedulePickerVisible },
              scheduleField
                ? new Date(scheduleField).toLocaleString()
                : "Informe a data e hora",
              !schedulePickerVisible
                ? null
                : el(
                    Popover,
                    { position: "middle left" },
                    el(DateTimePicker, {
                      currentDate: scheduleField,
                      onChange: onChangeSchedule,
                      className: "schedule-datepicker",
                    })
                  )
            )
          )
    ),
    el(
      "div",
      { className: "actions-control" },
      el(
        Button,
        { isPrimary: true, disabled: submitDisabledState, onClick: onSubmit },
        submitLabel
      ),
      spinnerActive ? el(Spinner) : null
    )
  );
};

const MorpheusMessageComponent = wp.compose.compose(
  wp.compose.withState({
    showNotices: null,
    titleField: "",
    titleValidateClass: "validate__error",
    messageField: "",
    messageValidateClass: "validate__error",
    linkField: "",
    linkValidateClass: "validate__error",
    isSchedule: false,
    schedulePickerVisible: false,
    scheduleField: null,
    submitLabel: "Enviar",
    submitDisabledState: false,
    spinnerActive: false,
  }),
  wp.data.withDispatch((dispatch, ownProps) => {
    const { titleField, messageField, linkField } = ownProps;
    return {
      titleValidate: () =>
        !titleField.length
          ? "validate__error"
          : titleField.length <= 38
          ? "validate__success"
          : "validate__warning",
      messageValidate: () =>
        !messageField.length
          ? "validate__error"
          : messageField.length <= 38
          ? "validate__success"
          : "validate__warning",
      linkValidate: () =>
        !linkField.length
          ? "validate__error"
          : linkField.length <= 38
          ? "validate__success"
          : "validate__warning",
    };
  })
)(MorpheusMessageForm);

ReactDOM.render(
  wp.element.createElement(MorpheusMessageComponent),
  document.getElementById("MorpheusPushMessageWidget")
);
