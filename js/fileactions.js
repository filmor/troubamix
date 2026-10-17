(function () {
  if (
    !window.OCA ||
    !window.OCA.Files ||
    !window.OCA.Files.fileActions ||
    !window.OC ||
    typeof window.OC.generateUrl !== "function"
  ) {
    return;
  }

  window.OCA.Files.fileActions.registerAction({
    name: "open_troubamix",
    displayName: t("troubamix", "Open with Troubamix"),
    mime: "application/json",
    permissions: window.OC.PERMISSION_READ,
    iconClass: "icon-play",
    actionHandler: function (fileName, context) {
      if (
        fileName !== "troubamix.json" ||
        !context ||
        !context.fileList ||
        typeof context.fileList.getModelForFile !== "function"
      ) {
        return;
      }

      var file = context.fileList.getModelForFile(fileName);
      if (!file) {
        return;
      }

      var fileId = file.get("id");
      if (!fileId) {
        return;
      }

      window.location.assign(
        window.OC.generateUrl(
          "/apps/troubamix/mix/" + encodeURIComponent(String(fileId)),
        ),
      );
    },
  });
})();
