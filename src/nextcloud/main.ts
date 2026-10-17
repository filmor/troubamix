import { mount } from "svelte";
import App from "./App.svelte";
import "../app.css";

const target = document.getElementById("troubamix");

if (target) {
  const config = JSON.parse(target.dataset.config ?? "{}");
  mount(App, { target, props: { config } });
}
