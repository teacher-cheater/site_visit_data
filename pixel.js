(function () {
  // Проверяем наличие куки
  function getCookie(name) {
    const matches = document.cookie.match(
      new RegExp(
        `(?:^|; )${name.replace(/([.$?*|{}()[]\/+^])/g, "\\$1")}=([^;]*)`
      )
    );
    return matches ? decodeURIComponent(matches[1]) : undefined;
  }

  // Устанавливаем куки
  function setCookie(name, value, options = {}) {
    console.log(name, value, options);
    options = {
      path: "/",
      ...options,
    };

    if (options.expires instanceof Date) {
      options.expires = options.expires.toUTCString();
    }

    let updatedCookie = `${encodeURIComponent(name)}=${encodeURIComponent(
      value
    )}`;

    for (let optionKey in options) {
      updatedCookie += `; ${optionKey}`;
      const optionValue = options[optionKey];
      if (optionValue !== true) {
        updatedCookie += `=${optionValue}`;
      }
    }

    document.cookie = updatedCookie;
  }

  // Проверяем и устанавливаем куки, если их нет
  if (getCookie("visited")) {
    return;
  }
  setCookie("visited", "true", { expires: 365 });
})();
