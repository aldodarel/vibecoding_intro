import type { Config, ParameterValue, RouteParams } from 'ziggy-js';

declare global {
    function route(): Config;
    function route(name: undefined, params: undefined, absolute?: boolean, config?: Config): Config;
    function route<T extends string>(name: T, params?: RouteParams<T> | undefined, absolute?: boolean, config?: Config): string;
    function route<T extends string>(name: T, params?: ParameterValue | undefined, absolute?: boolean, config?: Config): string;
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        route: typeof route;
    }
}
