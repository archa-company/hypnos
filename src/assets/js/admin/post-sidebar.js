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

const TribunaIconHorizontalPaths = [
  el("path", {
    d: "M323.422,23.655v-0.951c0-7.097-2.381-12.622-7.144-16.528c-4.763-3.856-11.621-5.81-20.527-5.81h-18.444h-19.542h-16.571 c-1.541,0-2.79,1.25-2.79,2.79v94.421c0,1.541,1.249,2.79,2.79,2.79h16.571h19.542h18.444c11.189,0,19.83-2.605,25.868-7.867 c6.038-5.261,9.084-12.407,9.084-21.491v-1.496c0-14.235-8.685-23.245-24.807-24.372V43.85 C317.564,42.23,323.422,35.515,323.422,23.655z",
  }),
  el("path", {
    d: "M26.438,100.366h32.405c1.541,0,2.79-1.249,2.79-2.79V31.684h20.26c1.541,0,2.79-1.249,2.79-2.79V3.155 c0-1.541-1.249-2.79-2.79-2.79H3.338c-1.541,0-2.79,1.249-2.79,2.79v25.841c0,1.541,1.249,2.79,2.79,2.79h20.309v65.79 C23.647,99.117,24.897,100.366,26.438,100.366z",
  }),
  el("path", {
    d: "M182.383,34.63v-0.621c0-22.481-12.972-33.644-36.279-33.644H96.491c-1.541,0-2.79,1.249-2.79,2.79v94.421 c0,1.541,1.249,2.79,2.79,2.79h32.406c1.541,0,2.79-1.249,2.79-2.79V72.511h8.888l6.741,25.771c0.321,1.228,1.43,2.084,2.699,2.084 h31.902c1.906,0,3.251-1.869,2.646-3.677l-11.895-35.497C179.127,55.251,182.383,46.413,182.383,34.63z",
  }),
  el("path", {
    d: "M226.596,100.366c1.541,0,2.79-1.249,2.79-2.79V3.155c0-1.541-1.249-2.79-2.79-2.79h-32.404c-1.541,0-2.79,1.249-2.79,2.79 v94.421c0,1.541,1.249,2.79,2.79,2.79H226.596z",
  }),
  el("path", {
    d: "M374.812,0.365h-32.301c-1.541,0-2.79,1.249-2.79,2.79v61.861c0,25.427,14.42,37.366,44.342,37.366 c14.212,0,25.271-3.103,33.075-9.355c7.803-6.253,11.68-15.659,11.68-28.321V3.155c0-1.541-1.249-2.79-2.79-2.79H393.52 c-1.541,0-2.79,1.249-2.79,2.79V65.74c0,5.581-2.171,8.372-6.564,8.372c-4.393,0-6.563-2.791-6.563-8.372V3.155 C377.602,1.614,376.353,0.365,374.812,0.365z",
  }),
  el("path", {
    d: "M475.794,2.132c-0.421-1.066-1.449-1.767-2.595-1.767h-32.574c-1.54,0-2.789,1.249-2.789,2.79v94.421 c0,1.541,1.249,2.79,2.789,2.79H472.1c1.541,0,2.79-1.249,2.79-2.79V70.961l-1.396-14.833h1.396l16.315,42.449 c0.414,1.077,1.449,1.789,2.604,1.789h31.211c1.541,0,2.79-1.249,2.79-2.79V3.155c0-1.541-1.249-2.79-2.79-2.79h-31.474 c-1.541,0-2.79,1.249-2.79,2.79v19.64l1.498,17.26h-1.498L475.794,2.132z",
  }),
  el("path", {
    d: "M618.111,2.559c-0.28-1.28-1.415-2.193-2.726-2.193h-54.491c-1.305,0-2.435,0.904-2.722,2.176l-21.274,94.421 c-0.394,1.745,0.934,3.404,2.722,3.404h33.706c1.377,0,2.548-1.006,2.757-2.366l1.754-11.381l19.433,0.102l1.663,11.262 c0.202,1.369,1.376,2.384,2.76,2.384h34.383c1.782,0,3.107-1.648,2.726-3.389L618.111,2.559z",
  }),
];
const TribunaIconVerticalPaths = [
  el("path", {
    d: "M370.5068359,313.2783203v94.4213867c0,1.5410156,1.2490234,2.7900391,2.7900391,2.7900391h16.5708008h19.5419922 h18.4438477c11.1894531,0,19.8291016-2.6044922,25.8681641-7.8662109 c6.0380859-5.2617188,9.0839844-12.4072266,9.0839844-21.4912109v-1.4970703 c0-14.234375-8.6835938-23.2446289-24.8066406-24.371582v-1.2910156 c11.6679688-1.6201172,17.5253906-8.3349609,17.5253906-20.1953125v-0.9506836 c0-7.097168-2.3808594-12.6210938-7.1435547-16.5273438c-4.7636719-3.8569336-11.6201172-5.8105469-20.5273438-5.8105469 H409.409668h-19.5419922H373.296875C371.7558594,310.4887695,370.5068359,311.7387695,370.5068359,313.2783203z",
  }),
  el("path", {
    d: "M306.8603516,296.2597656h32.4038086c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7885742V227.578125h20.2602539 c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7900391v-25.7382813c0-1.5410156-1.2490234-2.7900391-2.7900391-2.7900391 h-78.5541992c-1.5410156,0-2.7900391,1.2490234-2.7900391,2.7900391v25.8413086 c0,1.5410156,1.2490234,2.7900391,2.7900391,2.7900391h20.3100586v65.7900391 C304.0703125,295.0107422,305.3193359,296.2597656,306.8603516,296.2597656z",
  }),
  el("path", {
    d: "M462.8056641,230.5249023v-0.6196289c0-22.4819336-12.9716797-33.6450195-36.2802734-33.6450195h-49.6137695 c-1.5410156,0-2.7900391,1.2490234-2.7900391,2.7900391v94.4208984c0,1.5400391,1.2490234,2.7890625,2.7900391,2.7890625 h32.4057617c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7890625v-25.065918h8.8881836l6.7416992,25.7719727 c0.3203125,1.2280273,1.4306641,2.0830078,2.6982422,2.0830078h31.9033203c1.9052734,0,3.2509766-1.8681641,2.6445313-3.6762695 l-11.8935547-35.4970703C459.5498047,251.1450195,462.8056641,242.3061523,462.8056641,230.5249023z",
  }),
  el("path", {
    d: "M474.6142578,296.2602539h32.4033203c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7890625V199.050293 c0-1.5410156-1.2490234-2.7900391-2.7900391-2.7900391h-32.4033203c-1.5410156,0-2.7900391,1.2490234-2.7900391,2.7900391 v94.4208984C471.8242188,295.0112305,473.0732422,296.2602539,474.6142578,296.2602539z",
  }),
  el("path", {
    d: "M558.1308594,310.4887695h-32.5097656c-1.5410156,0-2.7900391,1.25-2.7900391,2.7895508v62.5854492 c0,5.5810547-2.1708984,8.3720703-6.5625,8.3720703c-4.3935547,0-6.5644531-2.7910156-6.5644531-8.3720703v-62.5854492 c0-1.5395508-1.25-2.7895508-2.7900391-2.7895508h-32.3007813c-1.5410156,0-2.7900391,1.25-2.7900391,2.7895508v61.8608398 c0,25.4277344,14.4189453,37.3662109,44.3417969,37.3662109c14.2119141,0,25.2714844-3.1015625,33.0751953-9.3544922 c7.8027344-6.2529297,11.6796875-15.6591797,11.6796875-28.3212891v-61.5512695 C560.9199219,311.7387695,559.6699219,310.4887695,558.1308594,310.4887695z",
  }),
  el("path", {
    d: "M280.9697266,521.9282227c0,1.5410156,1.2490234,2.7900391,2.7900391,2.7900391h31.4741211 c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7900391v-26.6142578l-1.3950195-14.8330078h1.3950195l16.315918,42.4482422 c0.4140625,1.078125,1.4487305,1.7890625,2.6030273,1.7890625h31.2109375c1.5410156,0,2.7900391-1.2490234,2.7900391-2.7900391 v-94.4199219c0-1.5410156-1.2490234-2.7900391-2.7900391-2.7900391h-31.4741211 c-1.5410156,0-2.7900391,1.2490234-2.7900391,2.7900391v19.6396484l1.4980469,17.2587891h-1.4980469l-14.9619141-37.9228516 c-0.4199219-1.0654297-1.4487305-1.765625-2.5947266-1.765625h-32.5732422c-1.5410156,0-2.7900391,1.2490234-2.7900391,2.7900391 V521.9282227z",
  }),
  el("path", {
    d: "M458.5185547,424.7182617h-54.4912109c-1.3037109,0-2.4335938,0.9042969-2.7207031,2.1757813l-21.2749023,94.4199219 c-0.3930664,1.7451172,0.9331055,3.4042969,2.7216797,3.4042969h33.7060547c1.3759766,0,2.5483398-1.0039063,2.7573242-2.3662109 l1.7539063-11.3798828l19.4316406,0.1025391l1.6630859,11.2617188c0.2011719,1.3681641,1.3759766,2.3818359,2.7597656,2.3818359 h34.3837891c1.78125,0,3.1054688-1.6464844,2.7246094-3.3867188l-20.6894531-94.4208984 C460.9648438,425.6303711,459.8310547,424.7182617,458.5185547,424.7182617z",
  }),
];
const TribunaIcon = el(
  "svg",
  { width: 20, height: 20, x: 0, y: 0, viewBox: "300 200 240 320" },
  TribunaIconVerticalPaths
);
const TribunaIconTitle = el(
  "svg",
  { width: 100, fill: "#0162fb", x: 0, y: 0, viewBox: "0 0 638.762 102.438" },
  TribunaIconHorizontalPaths
);

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
const TrbMessageForm = ({
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

    const providers = ["OneSignal"];
    // const providers = ["OneSignal", "SendPulse"];
    const promises = [];
    for (const provider of providers) {
      promises.push(sendToApiMessage({ provider, ...sendData }));
    }
    await Promise.all(promises);
  };

  const sendToApiMessage = async (message) => {
    try {
      const response = await apiFetch({
        method: "POST",
        url: `${window.location.origin}/wp-json/tribuna/v1/push-message`,
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
        `${provider}: Houston, estamos com problema! Não foi possível enviar a mensagem.`,
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
const TrbMessageComponent = compose(
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
)(TrbMessageForm);

/**
 *
 * PAINEL DO ENCURTADOR DE LINKS
 *
 */
const TrbBitlyForm = ({ setState, spinnerActive, linkList }) => {
  /**
   * Executa a ação principal
   */
  const onSubmit = async (source) => {
    /**
     * Valida se o post está publicado
     */
    if (
      wp.data.select("core/editor").getEditedPostAttribute("status") !=
      "publish"
    ) {
      createNotice(
        "Este post não está publicado. Você precisa publica-lo primeiro",
        "error"
      );
      return false;
    }

    await setState({
      spinnerActive: true,
    });

    try {
      const response = await apiFetch({
        path: "/tribuna/v1/shorter",
        method: "POST",
        data: {
          link: wp.data.select("core/editor").getPermalink(),
          postId: wp.data.select("core/editor").getCurrentPostId(),
          source: source,
        },
      });

      await updateLinkListElements(response);

      await setState({
        spinnerActive: false,
      });
    } catch (error) {
      createNotice(
        "Houston, estamos com problema! Não foi possível acessar o serviço.",
        "error"
      );
      console.error(error);
      await setState({
        spinnerActive: false,
      });
    }
  };

  const updateLinkListElements = async (link) => {
    console.log(link);

    const element = el(
      "li",
      { className: `linkitem linkitem--${link.sourceName}` },
      el("div", { className: "linkitem__title" }, link.sourceTitle),
      el("input", {
        type: "text",
        value: link.short,
        readonly: true,
        className:
          "linkitem__link linkitem__link--short components-text-control__input",
      }),
      el("input", {
        type: "text",
        value: link.long,
        readonly: true,
        className:
          "linkitem__link linkitem__link--long components-text-control__input",
      })
    );

    await setState({
      linkList: [element, ...linkList],
    });
  };

  const onReset = async () =>
    setState({
      spinnerActive: false,
      linkList: [],
    });

  return el(
    PanelBody,
    { title: "Encurtador de Link", icon: "admin-links", intialOpen: true },
    [
      el(
        "div",
        { className: "actions-control" },
        el("div", null, "Selecione o destino:"),
        spinnerActive
          ? el(Spinner)
          : el(DropdownMenu, {
              icon: "external",
              label: "Encurtar",
              controls: [
                {
                  title: "Facebook",
                  icon: "facebook",
                  onClick: () =>
                    onSubmit({ title: "Facebook", name: "facebook" }),
                },
                {
                  title: "Twitter",
                  icon: "twitter",
                  onClick: () =>
                    onSubmit({ title: "Twitter", name: "twitter" }),
                },
                {
                  title: "Youtube",
                  icon: "video-alt3",
                  onClick: () =>
                    onSubmit({ title: "Youtube", name: "youtube" }),
                },
                {
                  title: "WhatsApp",
                  icon: "format-chat",
                  onClick: () =>
                    onSubmit({ title: "WhatsApp", name: "whatsapp" }),
                },
                {
                  title: "Instagram",
                  icon: "instagram",
                  onClick: () =>
                    onSubmit({ title: "Instagram", name: "instagram" }),
                },
                {
                  title: "Newsletter",
                  icon: "email",
                  onClick: () =>
                    onSubmit({ title: "Newsletter", name: "newsletter" }),
                },
                {
                  title: "Messenger",
                  icon: "format-status",
                  onClick: () =>
                    onSubmit({ title: "Messenger", name: "messenger" }),
                },
              ],
            })
      ),
      !linkList.length
        ? null
        : el(
            "div",
            { className: "links-action" },
            el(Button, { isLink: true, onClick: onReset }, "Limpar")
          ),
      el("ul", { className: "links-control" }, linkList),
    ]
  );
};
const TrbBitlyComponent = compose(
  withState({
    spinnerActive: false,
    linkList: [],
  })
)(TrbBitlyForm);

/**
 *
 * PAINEL DE CAMPOS DA METÉRIA
 *
 */
const TrbPostForm = (props) => {
  return el(
    PluginDocumentSettingPanel,
    { title: "Recomendação", icon: "flag", intialOpen: true },
    [
      el(SelectControl, {
        label: "Tipo",
        value: props.typeField,
        onChange: (value) => props.onTypeFieldChange(value),
        options: [
          { label: "Hardnews", value: "hardnews" },
          { label: "Softnews", value: "softnews" },
          { label: "Quicknews", value: "quicknews" },
          { label: "Evergreen", value: "evergreen" },
        ],
      }),
      el(SelectControl, {
        label: "Cidade",
        value: props.cityField,
        onChange: (value) => props.onCityFieldChange(value),
        options: [
          { label: "Curitiba", value: "Curitiba" },
          { label: "Almirante Tamandaré", value: "Almirante Tamandaré" },
          { label: "Araucária", value: "Araucária" },
          { label: "Campo Largo", value: "Campo Largo" },
          { label: "Campo Magro", value: "Campo Magro" },
          { label: "Colombo", value: "Colombo" },
          { label: "Fazenda Rio Grande", value: "Fazenda Rio Grande" },
          { label: "Pinhais", value: "Pinhais" },
          { label: "São José dos Pinhais", value: "São José dos Pinhais" },
          { label: "Litoral do Paraná", value: "Litoral" },
          { label: "Brasil (Nacional)", value: "Brasil" },
        ],
      }),
    ]
  );
};
const TrbPostComponent = compose(
  // withState({
  //   typeField: wp.data.select('core/editor').getEditedPostAttribute('meta')['post_aspect_type'],
  //   cityField: wp.data.select('core/editor').getEditedPostAttribute('meta')['post_city'],
  // }),
  withSelect((select) => {
    return {
      typeField:
        select("core/editor").getEditedPostAttribute("meta")[
          "post_aspect_type"
        ],
      cityField:
        select("core/editor").getEditedPostAttribute("meta")["post_city"],
    };
  }),
  withDispatch((dispatch) => {
    return {
      onTypeFieldChange: (value) => {
        dispatch("core/editor").editPost({ meta: { post_aspect_type: value } });
      },
      onCityFieldChange: (value) => {
        dispatch("core/editor").editPost({ meta: { post_city: value } });
      },
    };
  })
)(TrbPostForm);

/**
 *
 * REGISTRO DO SIDEBAR DA TRIBUNA
 *
 */
registerPlugin("tribuna-sidebar", {
  icon: TribunaIcon,
  render: () => [
    el(PluginSidebarMoreMenuItem, { target: "tribuna-sidebar" }, "Tribuna"),
    el(
      PluginSidebar,
      {
        name: "tribuna-sidebar",
        title: TribunaIconTitle,
        className: "tribuna-sidebar",
      },
      [el(TrbMessageComponent), el(TrbBitlyComponent)]
    ),
  ],
});

registerPlugin("tribuna-postdata", {
  // icon: TribunaIcon,
  render: () => el(TrbPostComponent),
});
