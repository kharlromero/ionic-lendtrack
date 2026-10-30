import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  return {
    plugins: [react()],
    define: {
      __LENDTRACK_URL__: JSON.stringify(env.VITE_LENDTRACK_URL || 'http://localhost:8000'),
    },
    server: { host: '0.0.0.0', port: 8100 },
  };
});
