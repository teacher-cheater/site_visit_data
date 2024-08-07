(function () {
  console.log(window.navigator);

  const startTime = new Date().getTime();

  function getCookie(name) {
    const matches = document.cookie.match(
      new RegExp(
        `(?:^|; )${name.replace(/([.$?*|{}()[]\/+^])/g, "\\$1")}=([^;]*)`
      )
    );
    return matches ? decodeURIComponent(matches[1]) : undefined;
  }

  function setCookie(name, value, options = {}) {
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

  if (getCookie("visited")) {
    return;
  }
  setCookie("visited", "true", { expires: 365 });

  const data = {
    url: window.location.href,
    time_on_page: 0,
    ip_user: "",
    time_stamp: new Date().toISOString(),
    scroll_percentage: 0,
    history_click: "",
    user_agent: navigator.userAgent,
  };

  function sendData() {
    data.time_on_page = (new Date().getTime() - startTime) / 1000;
    fetch("http://site-visit-data/collect.php", {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json;charset=utf-8",
      },
      body: JSON.stringify(data),
    })
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then(data => {
        console.log("Success:", data);
      })
      .catch(error => {
        console.error("Error:", error);
      });
  }

  window.addEventListener("beforeunload", sendData);
})();
