import { useCallback, useEffect, useRef, useState } from 'react';
import {
  IonApp, IonButton, IonButtons, IonContent, IonHeader, IonIcon,
  IonLabel, IonPage, IonSpinner, IonTitle, IonToolbar, IonToast,
} from '@ionic/react';
import { arrowBackOutline, openOutline, refreshOutline, wifiOutline } from 'ionicons/icons';
import { App as CapacitorApp } from '@capacitor/app';
import { Browser } from '@capacitor/browser';

// Set VITE_LENDTRACK_URL in ionic-app/.env. Laravel remains the source of truth
// for authentication, authorization, validation, loan calculations and payments.
declare const __LENDTRACK_URL__: string;
const BASE_URL = __LENDTRACK_URL__.replace(/\/$/, '');

export default function App() {
  const frameRef = useRef<HTMLIFrameElement>(null);
  const [loading, setLoading] = useState(true);
  const [toast, setToast] = useState('');
  const [lastLoaded, setLastLoaded] = useState(Date.now());

  const reload = useCallback(() => {
    setLoading(true);
    setLastLoaded(Date.now());
    try { frameRef.current?.contentWindow?.location.reload(); }
    catch { setToast('Reload the page if the connection does not recover.'); }
  }, []);

  const goBack = useCallback(() => {
    try {
      const frameWindow = frameRef.current?.contentWindow;
      if (frameWindow && frameWindow.history.length > 1) frameWindow.history.back();
      else setToast('Use the LendTrack navigation to choose a page.');
    } catch { setToast('Use the LendTrack navigation to choose a page.'); }
  }, []);

  useEffect(() => {
    const listener = CapacitorApp.addListener('backButton', () => goBack());
    return () => { void listener.then(handle => handle.remove()); };
  }, [goBack]);

  const openInBrowser = async () => {
    try { await Browser.open({ url: BASE_URL }); }
    catch { window.open(BASE_URL, '_blank', 'noopener,noreferrer'); }
  };

  return (
    <IonApp>
      <IonPage>
        <IonHeader>
          <IonToolbar color="dark">
            <IonTitle>LendTrack</IonTitle>
            <IonButtons slot="end">
              <IonButton aria-label="Go back" onClick={goBack}><IonIcon icon={arrowBackOutline} /></IonButton>
              <IonButton aria-label="Refresh" onClick={reload}><IonIcon icon={refreshOutline} /></IonButton>
              <IonButton aria-label="Open in browser" onClick={openInBrowser}><IonIcon icon={openOutline} /></IonButton>
            </IonButtons>
          </IonToolbar>
        </IonHeader>
        <IonContent fullscreen>
          {loading && <div className="loading-overlay"><IonSpinner name="crescent" /><IonLabel>Connecting to LendTrack…</IonLabel></div>}
          <iframe
            key={lastLoaded}
            ref={frameRef}
            className="lendtrack-frame"
            src={BASE_URL}
            title="LendTrack application"
            onLoad={() => setLoading(false)}
            onError={() => { setLoading(false); setToast('Could not connect. Check the backend URL and that Laravel is running.'); }}
            allow="clipboard-read; clipboard-write"
          />
          <div className="connection-hint"><IonIcon icon={wifiOutline} /> Connected to your LendTrack server</div>
        </IonContent>
        <IonToast isOpen={Boolean(toast)} message={toast} duration={2800} onDidDismiss={() => setToast('')} />
      </IonPage>
    </IonApp>
  );
}
