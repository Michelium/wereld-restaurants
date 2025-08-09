import {registerControllers, startStimulusApp} from "vite-plugin-symfony/stimulus/helpers"
import {registerReactControllerComponents} from "vite-plugin-symfony/stimulus/helpers/react"

registerReactControllerComponents(import.meta.glob('./react/controllers/**/*.ts(x)\?'));

const app = startStimulusApp();
registerControllers(
    app,
    import.meta.glob(
        "./controllers/*_controller.js",
        {
            query: "?stimulus",
            eager: true,
        },
    ),
);
