import { useAlert } from "./AlertContext";

export default function AlertMessage() {
  const { alert } = useAlert();

  if (!alert.show) return null;
 // console.log("ALERT_SHOW:", alert.show);
 // console.log("MESSAGE:", alert.message);

  return (
    <div
     className={`
        fixed
        bottom-8
        right-8
        min-w-[320px]
        max-w-[420px]
        px-6
        py-4
        rounded-xl
        shadow-2xl
        text-white
        text-lg
        font-medium
        z-[999999]
        transition-all
        duration-300
        ${alert.type === "success" ? "bg-green-600" : "bg-red-600"}
      `}
    >
      {alert.message}
    </div>
  );
}
