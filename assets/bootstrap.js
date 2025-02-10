import { startStimulusApp } from '@symfony/stimulus-bundle';

export const app = startStimulusApp(require.context())
