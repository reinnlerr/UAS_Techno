import { router } from 'expo-router';
import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function TicketScreen() {
  return (
    <View style={styles.container}>
      <View style={styles.card}>
        <Text style={styles.icon}>🎉</Text>
        <Text style={styles.title}>Reservasi Berhasil!</Text>
        <Text style={styles.subtitle}>
          Lapangan telah dipesan. Tunjukkan e-tiket ini kepada admin lapangan saat kedatangan.
        </Text>

        <View style={styles.ticketInfo}>
          <Text style={styles.infoText}>🏟 Stadium Arena (Lapangan A)</Text>
          <Text style={styles.infoText}>📅 Jumat, 25 Okt 2024</Text>
          <Text style={styles.infoText}>⏰ 19:00 - 20:00 WIB</Text>
          <Text style={styles.infoText}>💰 Rp 150.000 (Bayar di Lokasi)</Text>
        </View>

        <TouchableOpacity 
          style={styles.button} 
          onPress={() => router.back()}
        >
          <Text style={styles.buttonText}>Kembali ke Beranda</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#006d37', // Hijau FasilBook
    justifyContent: 'center',
    padding: 20,
  },
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    elevation: 5,
  },
  icon: {
    fontSize: 64,
    marginBottom: 16,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#181c1c',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 14,
    color: '#5d5e61',
    textAlign: 'center',
    marginBottom: 24,
    lineHeight: 20,
  },
  ticketInfo: {
    backgroundColor: '#f1f4f3',
    padding: 16,
    borderRadius: 8,
    width: '100%',
    marginBottom: 24,
    borderWidth: 1,
    borderColor: '#e6e9e8',
    borderStyle: 'dashed',
  },
  infoText: {
    fontSize: 16,
    color: '#3d4a3e',
    marginBottom: 8,
    fontWeight: '600',
  },
  button: {
    backgroundColor: '#fe6b00',
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 12,
    width: '100%',
    alignItems: 'center',
  },
  buttonText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});