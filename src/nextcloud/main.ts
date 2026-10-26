import {
  DefaultType,
  FileAction,
  Permission,
  registerFileAction,
} from "@nextcloud/files";
import { t } from "@nextcloud/l10n";
import { generateUrl } from "@nextcloud/router";
import { mount } from "svelte";
import App from "./App.svelte";
import "../app.css";

registerFileAction(
  new FileAction({
    id: "open_troubamix",
    displayName: () => t("troubamix", "Open with Troubamix"),
    iconSvgInline: () =>
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M8 5v14l11-7z"/></svg>',
    default: DefaultType.DEFAULT,
    enabled: (nodes) =>
      nodes.length === 1 &&
      nodes[0].basename.toLowerCase().endsWith(".tbmx") &&
      nodes[0].mime === "application/x-troubamix" &&
      (nodes[0].permissions & Permission.READ) === Permission.READ,
    exec: async (node) => {
      if (!node.fileid) {
        return false;
      }

      window.location.assign(
        generateUrl("/apps/troubamix/mix/{fileId}", {
          fileId: String(node.fileid),
        }),
      );
      return true;
    },
  }),
);

const target = document.getElementById("troubamix");

if (target) {
  const config = JSON.parse(target.dataset.config ?? "{}");
  mount(App, { target, props: { config } });
}
