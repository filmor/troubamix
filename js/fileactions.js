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

  var mimeType = "application/x-troubamix";
  var actionName = "open_troubamix";

  window.OCA.Files.fileActions.registerAction({
    name: actionName,
    displayName: t("troubamix", "Open with Troubamix"),
    mime: mimeType,
    permissions: window.OC.PERMISSION_READ,
    iconClass: "icon-play",
    actionHandler: function (fileName, context) {
      if (
        !fileName.toLowerCase().endsWith(".tbmx") ||
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

  window.OCA.Files.fileActions.setDefault(mimeType, actionName);
})();
