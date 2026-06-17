// Import the functions you need from the SDKs you need
import { getAnalytics } from "firebase/analytics";
import { initializeApp } from "firebase/app";
const firebaseConfig = {
  apiKey: "AIzaSyBilXEWeGMoTn4_MSK-2YLVFispxh3dieg",
  authDomain: "fasilbook-f816a.firebaseapp.com",
  projectId: "fasilbook-f816a",
  storageBucket: "fasilbook-f816a.firebasestorage.app",
  messagingSenderId: "1016775088429",
  appId: "1:1016775088429:web:edb50d1d1b66642037dba4",
  measurementId: "G-V6GQZ27ZNT"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);