import axios from "axios";
const apiKey = import.meta.env.VITE_API_KEY;

export async function uploadImage(file) {
  if (!file) {
    throw new Error("Aucun fichier fourni");
  }

  const formData = new FormData();
  formData.append("image", file);

  try {
    const response = await axios.post(
      `https://api.imgbb.com/1/upload?key=${apiKey}`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );

    return response.data.data.url;
  } catch (error) {
    console.error("Erreur upload image:", error);
    throw new Error("Échec de l'upload de l'image");
  }
}

export function base64ToFile(base64, filename = "image.jpg") {
  const arr = base64.split(",");
  const mime = arr[0].match(/:(.*?);/)[1];
  const bstr = atob(arr[1]);
  let n = bstr.length;
  const u8arr = new Uint8Array(n);

  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }

  return new File([u8arr], filename, { type: mime });
}
